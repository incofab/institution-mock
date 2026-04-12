<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamActivation extends BaseModel
{
  use HasFactory;

  protected $casts = [
    'institution_id' => 'integer',
    'event_id' => 'integer',
    'activated_by_user_id' => 'integer',
    'num_of_exams' => 'integer',
    'licenses' => 'integer',
    'license_balance_before' => 'integer',
    'license_balance_after' => 'integer',
    'activated_at' => 'datetime',
  ];

  function institution()
  {
    return $this->belongsTo(Institution::class);
  }

  function event()
  {
    return $this->belongsTo(Event::class);
  }

  function exams()
  {
    return $this->hasMany(Exam::class);
  }

  function activatedByUser()
  {
    return $this->belongsTo(User::class, 'activated_by_user_id');
  }
}
