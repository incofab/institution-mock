<?php

use App\Enums\InstitutionUserRole;
use App\Models\Institution;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('allows an institution admin to assign users with roles', function () {
  $institution = Institution::factory()
    ->user()
    ->create();
  $admin = $institution->institutionUsers()->first()->user;
  $staff = User::factory()->create();
  actingAs($admin);

  $this->post(route('institutions.users.store', $institution), [
    'email' => $staff->email,
    'role' => InstitutionUserRole::Staff->value,
  ])->assertRedirect(route('institutions.users.index', $institution));

  $this->assertDatabaseHas('institution_users', [
    'institution_id' => $institution->id,
    'user_id' => $staff->id,
    'role' => InstitutionUserRole::Staff->value,
  ]);
});

it('prevents institution staff from assigning users', function () {
  $institution = Institution::factory()->create();
  $staff = User::factory()->create();
  $newUser = User::factory()->create();
  $institution->institutionUsers()->create([
    'user_id' => $staff->id,
    'role' => InstitutionUserRole::Staff,
  ]);
  actingAs($staff);

  $this->post(route('institutions.users.store', $institution), [
    'email' => $newUser->email,
    'role' => InstitutionUserRole::Admin->value,
  ])->assertForbidden();

  $this->assertDatabaseMissing('institution_users', [
    'institution_id' => $institution->id,
    'user_id' => $newUser->id,
  ]);
});
