<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class SetUserRole extends Command
{
  protected $signature = 'users:set-role
    {email : Email address of the existing user}
    {role : Role to assign: admin, manager, staff, or none}';

  protected $description = 'Set a role for an existing user';

  function handle(): int
  {
    $email = strtolower(trim($this->argument('email')));
    $role = strtolower(trim($this->argument('role')));

    $userRole = match ($role) {
      'none', 'null' => null,
      default => UserRole::tryFrom($role),
    };

    if ($role && !$userRole && !in_array($role, ['none', 'null'])) {
      $this->error('Invalid role. Use admin, manager, staff, or none.');
      return self::FAILURE;
    }

    $user = User::query()
      ->whereRaw('LOWER(email) = ?', [$email])
      ->first();

    if (!$user) {
      $this->error("No user found with email {$email}.");
      return self::FAILURE;
    }

    $user->forceFill(['role' => $userRole])->save();

    $roleLabel = $userRole?->value ?? 'none';
    $this->info("{$user->email} role set to {$roleLabel}.");

    return self::SUCCESS;
  }
}
