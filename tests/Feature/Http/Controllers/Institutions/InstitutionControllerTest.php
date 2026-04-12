<?php

use App\Models\Event;
use App\Models\ExamActivation;
use App\Models\Funding;
use App\Models\GatewayPayment;
use App\Models\Institution;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

use function Pest\Laravel\actingAs;

it('shows the license balance on the institution dashboard', function () {
  $institution = Institution::factory()
    ->user()
    ->create(['licenses' => 17]);
  $user = $institution->institutionUsers()->first()->user;
  actingAs($user);

  $this->get(route('institutions.dashboard', $institution))
    ->assertOk()
    ->assertViewIs('institutions.index')
    ->assertViewHas('licenses_count', 17)
    ->assertSee('Licenses')
    ->assertSee('17');
});

it('shows activation history for the institution', function () {
  $institution = Institution::factory()
    ->user()
    ->create(['licenses' => 17]);
  $user = $institution->institutionUsers()->first()->user;
  $event = Event::factory()
    ->institution($institution)
    ->create(['title' => 'Entrance Exam']);
  ExamActivation::query()->create([
    'institution_id' => $institution->id,
    'event_id' => $event->id,
    'activated_by_user_id' => $user->id,
    'num_of_exams' => 3,
    'licenses' => 3,
    'license_balance_before' => 17,
    'license_balance_after' => 14,
  ]);
  actingAs($user);

  $this->get(route('institutions.activation-history', $institution))
    ->assertOk()
    ->assertViewIs('institutions.activation-history')
    ->assertSee('Entrance Exam')
    ->assertSee('3');
});

it('shows funding history for the institution', function () {
  $institution = Institution::factory()
    ->user()
    ->create(['licenses' => 7]);
  $user = $institution->institutionUsers()->first()->user;
  Funding::query()->create([
    'institution_id' => $institution->id,
    'user_id' => $user->id,
    'amount' => 450,
    'license_cost' => 200,
    'num_of_licenses' => 2,
    'balance_amount' => 50,
    'license_balance_before' => 5,
    'license_balance_after' => 7,
    'source' => 'manual',
  ]);
  actingAs($user);

  $this->get(route('institutions.funding-history', $institution))
    ->assertOk()
    ->assertViewIs('institutions.funding-history')
    ->assertSee('450.00')
    ->assertSee('50.00');
});

it('shows the online license funding form', function () {
  $institution = Institution::factory()
    ->user()
    ->create(['license_cost' => 200]);
  $user = $institution->institutionUsers()->first()->user;
  actingAs($user);

  $this->get(route('institutions.fund-licenses.create', $institution))
    ->assertOk()
    ->assertViewIs('institutions.fund-licenses')
    ->assertSee('Paystack')
    ->assertSee('Monnify')
    ->assertSee('Flutterwave');
});

it('initializes online license funding with paystack', function () {
  config(['services.paystack.secret_key' => 'test-secret']);
  Http::fake([
    'https://api.paystack.co/transaction/initialize' => Http::response(
      [
        'status' => true,
        'data' => [
          'authorization_url' => 'https://checkout.paystack.test/pay',
          'reference' => 'LIC-1-REFERENCE',
        ],
      ],
      200,
    ),
  ]);

  $institution = Institution::factory()
    ->user()
    ->create(['licenses' => 4, 'license_cost' => 200]);
  $user = $institution->institutionUsers()->first()->user;
  actingAs($user);

  $this->post(route('institutions.fund-licenses.store', $institution), [
    'amount' => 450,
    'gateway' => 'paystack',
  ])->assertRedirect('https://checkout.paystack.test/pay');

  $this->assertDatabaseCount('fundings', 0);
  $this->assertDatabaseHas('gateway_payments', [
    'institution_id' => $institution->id,
    'user_id' => $user->id,
    'amount' => 450,
    'gateway' => 'paystack',
    'status' => GatewayPayment::STATUS_INITIALIZED,
  ]);
  $this->assertFalse(Schema::hasColumn('gateway_payments', 'license_cost'));
  $this->assertFalse(Schema::hasColumn('gateway_payments', 'num_of_licenses'));
  $this->assertFalse(Schema::hasColumn('gateway_payments', 'balance_amount'));
  $this->assertDatabaseHas('institutions', [
    'id' => $institution->id,
    'licenses' => 4,
  ]);
});

it('credits licenses after paystack callback verification', function () {
  config(['services.paystack.secret_key' => 'test-secret']);
  Http::fake([
    'https://api.paystack.co/transaction/verify/LIC-VERIFY-REFERENCE' => Http::response(
      [
        'status' => true,
        'data' => [
          'status' => 'success',
          'amount' => 45000,
          'gateway_response' => 'Successful',
        ],
      ],
      200,
    ),
  ]);

  $institution = Institution::factory()
    ->user()
    ->create(['licenses' => 4, 'license_cost' => 200]);
  $user = $institution->institutionUsers()->first()->user;
  GatewayPayment::query()->create([
    'institution_id' => $institution->id,
    'user_id' => $user->id,
    'amount' => 450,
    'gateway' => 'paystack',
    'reference' => 'LIC-VERIFY-REFERENCE',
    'status' => GatewayPayment::STATUS_INITIALIZED,
  ]);
  actingAs($user);

  $this->get(
    route('institutions.fund-licenses.callback', [
      $institution,
      'paystack',
      'reference' => 'LIC-VERIFY-REFERENCE',
    ]),
  )
    ->assertRedirect(route('institutions.funding-history', $institution))
    ->assertSessionHas('message', 'Institution licenses funded successfully');

  $this->assertDatabaseHas('fundings', [
    'institution_id' => $institution->id,
    'reference' => 'LIC-VERIFY-REFERENCE',
    'license_balance_before' => 4,
    'license_balance_after' => 6,
    'license_cost' => 200,
    'num_of_licenses' => 2,
    'balance_amount' => 50,
    'source' => 'paystack',
  ]);
  $this->assertDatabaseHas('gateway_payments', [
    'institution_id' => $institution->id,
    'reference' => 'LIC-VERIFY-REFERENCE',
    'status' => GatewayPayment::STATUS_CREDITED,
  ]);
  $this->assertDatabaseHas('institutions', [
    'id' => $institution->id,
    'licenses' => 6,
  ]);

  $this->get(
    route('institutions.fund-licenses.callback', [
      $institution,
      'paystack',
      'reference' => 'LIC-VERIFY-REFERENCE',
    ]),
  )->assertRedirect(route('institutions.funding-history', $institution));

  $this->assertDatabaseCount('fundings', 1);
  $this->assertDatabaseHas('institutions', [
    'id' => $institution->id,
    'licenses' => 6,
  ]);
});
