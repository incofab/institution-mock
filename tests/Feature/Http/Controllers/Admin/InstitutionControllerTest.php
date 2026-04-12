<?php

use App\Enums\InstitutionUserRole;
use App\Models\Course;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $admin = User::factory()->admin()->create();
    actingAs($admin);
});
test('index method returns view with paginated institutions', function () {
    Institution::factory()->count(10)->create();

    $response = $this->get(route('admin.institutions.index'));

    $response
        ->assertOk()
        ->assertViewIs('admin.institutions.index')
        ->assertViewHas('allRecords');
});

test('create method returns view for creating an institution', function () {
    $response = $this->get(route('admin.institutions.create'));

    $response->assertOk()->assertViewIs('admin.institutions.create');
});

test('store method validates and creates an institution', function () {
    $data = Institution::factory()->make()->toArray();

    $this->post(route('admin.institutions.store'), $data)
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'Record created successfully');

    $this->assertDatabaseCount('institutions', 1);
});

test('edit method updates an institution', function () {
    $institution = Institution::factory()->create();
    $response = $this->get(route('admin.institutions.edit', $institution));
    $response->assertOk()->assertViewIs('admin.institutions.create');
});

test('update method updates an institution and redirects', function () {
    $institution = Institution::factory()->create();
    $updatedData = [
        'name' => 'Updated Institution Name',
        'email' => 'updated@example.com',
        'licenses' => 25,
        'license_cost' => 250,
    ];

    $response = $this->put(
        route('admin.institutions.update', $institution),
        $updatedData,
    );

    $response
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'Record updated');

    $this->assertDatabaseHas('institutions', $updatedData);
});

test('license cost is required and cannot be zero', function () {
    $data = Institution::factory()
        ->make(['license_cost' => 0])
        ->toArray();

    $this->post(route('admin.institutions.store'), $data)
        ->assertSessionHasErrors('license_cost');
});

test(
    'destroy method deletes an institution without courses or events',
    function () {
        $institution = Institution::factory()->create();

        $response = $this->delete(
            route('admin.institutions.destroy', $institution),
        );

        $response
            ->assertRedirect(route('admin.institutions.index'))
            ->assertSessionHas('message', 'Delete institution');

        $this->assertDatabaseMissing('institutions', [
            'id' => $institution->id,
        ]);
    },
);

test('destroy method aborts if institution has courses or events', function () {
    $institution = Institution::factory()->create();
    Course::factory()->for($institution)->create();

    $response = $this->delete(
        route('admin.institutions.destroy', $institution),
    );

    $response->assertStatus(401);
    $this->assertDatabaseHas('institutions', ['id' => $institution->id]);
});

test('assignUserView method returns view with institution', function () {
    $institution = Institution::factory()->create();

    $response = $this->get(
        route('admin.institutions.assign-user', $institution),
    );

    $response
        ->assertOk()
        ->assertViewIs('admin.institutions.assign-user')
        ->assertViewHas('institution', $institution);
});

test('assignUserStore method assigns user to institution', function () {
    $institution = Institution::factory()->create();
    $user = User::factory()->create();

    $data = ['email' => $user->email, 'role' => InstitutionUserRole::Staff->value];

    $response = $this->post(
        route('admin.institutions.assign-user.store', $institution),
        $data,
    );

    $response
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'User assigned');

    $this->assertDatabaseHas('institution_users', [
        'institution_id' => $institution->id,
        'user_id' => $user->id,
        'role' => InstitutionUserRole::Staff->value,
    ]);
});

test('global admin manually funds institution licenses', function () {
    $institution = Institution::factory()->create([
        'licenses' => 3,
        'license_cost' => 200,
    ]);

    $this->post(route('admin.institutions.fund.store', $institution), [
        'amount' => 550,
        'bonus_licenses' => 3,
        'comment' => 'Introductory bonus',
    ])
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'Institution licenses funded successfully');

    $this->assertDatabaseHas('institutions', [
        'id' => $institution->id,
        'licenses' => 8,
    ]);
    $this->assertDatabaseHas('fundings', [
        'institution_id' => $institution->id,
        'amount' => 550,
        'license_cost' => 200,
        'num_of_licenses' => 5,
        'bonus_licenses' => 3,
        'balance_amount' => 150,
        'license_balance_before' => 3,
        'license_balance_after' => 8,
        'source' => 'manual',
        'comment' => 'Introductory bonus',
    ]);
});

test('global admin can give bonus licenses without funding amount', function () {
    $institution = Institution::factory()->create([
        'licenses' => 3,
        'license_cost' => 200,
    ]);

    $this->post(route('admin.institutions.fund.store', $institution), [
        'amount' => null,
        'bonus_licenses' => 4,
        'comment' => 'Support credit',
    ])
        ->assertRedirect(route('admin.institutions.index'))
        ->assertSessionHas('message', 'Institution licenses funded successfully');

    $this->assertDatabaseHas('institutions', [
        'id' => $institution->id,
        'licenses' => 7,
    ]);
    $this->assertDatabaseHas('fundings', [
        'institution_id' => $institution->id,
        'amount' => 0,
        'license_cost' => 200,
        'num_of_licenses' => 4,
        'bonus_licenses' => 4,
        'balance_amount' => 0,
        'license_balance_before' => 3,
        'license_balance_after' => 7,
        'source' => 'manual',
        'comment' => 'Support credit',
    ]);
});

test('global admin can view the manual funding page', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);

    $this->get(route('admin.institutions.fund', $institution))
        ->assertOk()
        ->assertViewIs('admin.institutions.fund')
        ->assertViewHas('institution', $institution);
});

test('global admin can view the invoice generation page', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);

    $this->get(route('admin.institutions.invoice', $institution))
        ->assertOk()
        ->assertViewIs('admin.institutions.invoice')
        ->assertViewHas('institution', $institution)
        ->assertSee('Extra Charges')
        ->assertSee('Generate Invoice');
});

test('global admin can generate an invoice with extra charges', function () {
    $institution = Institution::factory()->create([
        'licenses' => 0,
        'license_cost' => 200,
    ]);

    $this->post(route('admin.institutions.invoice.store', $institution), [
        'extra_charges' => [
            ['label' => 'Setup fee', 'amount' => 500],
        ],
    ])
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertSee('Setup fee')
        ->assertSee('500.00');
});

test('invoice extra charge requires label and amount together', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);

    $this->post(route('admin.institutions.invoice.store', $institution), [
        'extra_charges' => [
            ['label' => 'Setup fee', 'amount' => null],
        ],
    ])->assertSessionHasErrors('extra_charges.0.amount');
});

test('global admin can view funding history', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);
    Funding::query()->create([
        'institution_id' => $institution->id,
        'user_id' => currentUser()->id,
        'amount' => 600,
        'license_cost' => 200,
        'num_of_licenses' => 3,
        'balance_amount' => 0,
        'license_balance_before' => 0,
        'license_balance_after' => 3,
        'source' => 'manual',
    ]);

    $this->get(route('admin.fundings.index'))
        ->assertOk()
        ->assertViewIs('admin.institutions.funding-history')
        ->assertSee($institution->name)
        ->assertSee('600.00');
});

test('manual funding requires a global admin', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);
    $user = User::factory()->create();
    actingAs($user);

    $this->post(route('admin.institutions.fund.store', $institution), [
        'amount' => 400,
    ])->assertForbidden();

    $this->assertDatabaseMissing('fundings', [
        'institution_id' => $institution->id,
    ]);
});

test('manual funding amount must cover at least one license', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);

    $this->post(route('admin.institutions.fund.store', $institution), [
        'amount' => 199,
    ])->assertSessionHasErrors('amount');

    $this->assertDatabaseCount('fundings', 0);
});

test('manual funding requires amount or bonus licenses', function () {
    $institution = Institution::factory()->create(['license_cost' => 200]);

    $this->post(route('admin.institutions.fund.store', $institution), [
        'amount' => null,
        'bonus_licenses' => 0,
    ])->assertSessionHasErrors('amount');

    $this->assertDatabaseCount('fundings', 0);
});
