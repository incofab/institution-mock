<?php

namespace App\Models;

use App\Enums\InstitutionUserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InstitutionUser extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'institution_id' => 'integer',
        'user_id' => 'integer',
        'role' => InstitutionUserRole::class,
    ];

    function isAdmin(): bool
    {
        return $this->role === InstitutionUserRole::Admin;
    }

    function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    function user()
    {
        return $this->belongsTo(User::class);
    }
}
