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
