<?php

use App\Enums\InstitutionUserRole;
use App\Models\Institution;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows a normal user to create an institution as admin with default license cost', function () {
  $user = User::factory()->create();
  $institutionName = 'Self Service School ' . uniqid();
  actingAs($user);

  $this->post(route('users.institutions.store'), [
    'name' => $institutionName,
    'email' => 'school@example.com',
    'phone' => '08012345678',
    'address' => '15 Main Street',
    'license_cost' => 999,
    'licenses' => 50,
  ])->assertRedirect();

  $institution = Institution::query()
    ->where('name', $institutionName)
    ->firstOrFail();

  expect((float) $institution->license_cost)->toBe(300.0)
    ->and($institution->licenses)->toBe(0);

  $this->assertDatabaseHas('institution_users', [
    'institution_id' => $institution->id,
    'user_id' => $user->id,
    'role' => InstitutionUserRole::Admin->value,
  ]);
});

it('asks multi institution users to select an institution before entering the dashboard', function () {
  $user = User::factory()->create();
  Institution::factory()
    ->user($user)
    ->create(['name' => 'First School']);
  Institution::factory()
    ->user($user)
    ->create(['name' => 'Second School']);
  actingAs($user);

  $this->get(route('users.dashboard'))->assertRedirect(
    route('users.institutions.select'),
  );

  $this->get(route('users.institutions.select'))
    ->assertOk()
    ->assertViewIs('user.institution-select')
    ->assertSee('First School')
    ->assertSee('Second School');
});

it('switches the active institution for a multi institution user', function () {
  $user = User::factory()->create();
  $firstInstitution = Institution::factory()
    ->user($user)
    ->create(['name' => 'First School']);
  $secondInstitution = Institution::factory()
    ->user($user)
    ->create(['name' => 'Second School']);
  actingAs($user);

  $this->post(route('users.institutions.switch'), [
    'institution_id' => $secondInstitution->id,
  ])->assertRedirect(route('institutions.dashboard', $secondInstitution));

  $this->get(route('users.dashboard'))->assertRedirect(
    route('users.institutions.select'),
  );

  $this->get(route('institutions.dashboard', $firstInstitution))
    ->assertOk()
    ->assertSee('Switch Institution');
});

it('marks the current institution on the switch page from the selected institution flag', function () {
  $user = User::factory()->create();
  $firstInstitution = Institution::factory()
    ->user($user)
    ->create(['name' => 'First School']);
  Institution::factory()
    ->user($user)
    ->create(['name' => 'Second School']);
  actingAs($user);

  $this->get(
    route('users.institutions.select', [
      'selected_institution' => $firstInstitution->code,
    ]),
  )
    ->assertOk()
    ->assertSee('First School')
    ->assertSee('Current');
});

it('does not allow switching to an institution the user does not belong to', function () {
  $user = User::factory()->create();
  Institution::factory()
    ->user($user)
    ->create();
  $otherInstitution = Institution::factory()->create();
  actingAs($user);

  $this->post(route('users.institutions.switch'), [
    'institution_id' => $otherInstitution->id,
  ])->assertForbidden();
});
