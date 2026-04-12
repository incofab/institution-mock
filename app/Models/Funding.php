<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Funding extends BaseModel
{
  use HasFactory;

  protected $casts = [
    'institution_id' => 'integer',
    'user_id' => 'integer',
    'amount' => 'float',
    'license_cost' => 'float',
    'num_of_licenses' => 'integer',
    'bonus_licenses' => 'integer',
    'balance_amount' => 'float',
    'license_balance_before' => 'integer',
    'license_balance_after' => 'integer',
    'fundable_id' => 'integer',
  ];

  function institution()
  {
    return $this->belongsTo(Institution::class);
  }

  function user()
  {
    return $this->belongsTo(User::class);
  }

  function fundable()
  {
    return $this->morphTo();
  }
}
