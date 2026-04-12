<?php

use App\Enums\UserRole;
use App\Models\User;

it('grants global admin access to admin and manager roles only', function () {
  expect(new User(['role' => UserRole::Admin]))
    ->isAdmin()
    ->toBeTrue()
    ->and(new User(['role' => UserRole::Manager]))
    ->isAdmin()
    ->toBeTrue()
    ->and(new User(['role' => UserRole::Staff]))
    ->isAdmin()
    ->toBeFalse()
    ->and(new User(['role' => null]))
    ->isAdmin()
    ->toBeFalse();
});
