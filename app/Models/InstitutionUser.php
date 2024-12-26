<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstitutionUser extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'institution_id' => 'integer',
        'user_id' => 'integer',
    ];

    function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
