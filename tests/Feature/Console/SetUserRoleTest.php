<?php

use App\Enums\UserRole;
use App\Models\User;

it('sets an existing user role by email', function () {
  $user = User::factory()->create([
    'email' => 'manager@example.com',
    'role' => null,
  ]);

  $this->artisan('users:set-role', [
    'email' => 'manager@example.com',
    'role' => 'manager',
  ])
    ->expectsOutput('manager@example.com role set to manager.')
    ->assertExitCode(0);

  expect($user->fresh()->role)->toBe(UserRole::Manager);
});

it('clears an existing user role', function () {
  $user = User::factory()->create([
    'email' => 'staff@example.com',
    'role' => UserRole::Staff,
  ]);

  $this->artisan('users:set-role', [
    'email' => 'staff@example.com',
    'role' => 'none',
  ])
    ->expectsOutput('staff@example.com role set to none.')
    ->assertExitCode(0);

  expect($user->fresh()->role)->toBeNull();
});

it('returns failure when the user email does not exist', function () {
  $this->artisan('users:set-role', [
    'email' => 'missing@example.com',
    'role' => 'admin',
  ])
    ->expectsOutput('No user found with email missing@example.com.')
    ->assertExitCode(1);
});

it('returns failure when the role is invalid', function () {
  $this->artisan('users:set-role', [
    'email' => 'manager@example.com',
    'role' => 'owner',
  ])
    ->expectsOutput('Invalid role. Use admin, manager, staff, or none.')
    ->assertExitCode(1);
});
