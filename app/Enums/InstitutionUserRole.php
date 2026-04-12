<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum InstitutionUserRole: string
{
  use EnumToArray;

  case Admin = 'admin';
  case Staff = 'staff';

  public function label(): string
  {
    return match ($this) {
      self::Admin => 'Admin',
      self::Staff => 'Staff',
    };
  }
}
