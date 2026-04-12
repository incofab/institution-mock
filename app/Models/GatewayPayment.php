<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class GatewayPayment extends BaseModel
{
  use HasFactory;

  const STATUS_PENDING = 'pending';
  const STATUS_INITIALIZED = 'initialized';
  const STATUS_CREDITED = 'credited';
  const STATUS_FAILED = 'failed';

  protected $casts = [
    'institution_id' => 'integer',
    'user_id' => 'integer',
    'amount' => 'float',
    'gateway_payload' => 'array',
    'initialized_at' => 'datetime',
    'verified_at' => 'datetime',
    'failed_at' => 'datetime',
  ];

  function institution()
  {
    return $this->belongsTo(Institution::class);
  }

  function user()
  {
    return $this->belongsTo(User::class);
  }

  function funding()
  {
    return $this->morphOne(Funding::class, 'fundable');
  }
}
