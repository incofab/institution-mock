<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum UserRole: string
{
  use EnumToArray;

  case Admin = 'admin';
  case Manager = 'manager';
  case Staff = 'staff';

  public function label(): string
  {
    return match ($this) {
      self::Admin => 'Admin',
      self::Manager => 'Manager',
      self::Staff => 'Staff',
    };
  }

  public function hasGlobalAdminAccess(): bool
  {
    return match ($this) {
      self::Admin, self::Manager => true,
      self::Staff => false,
    };
  }
}
