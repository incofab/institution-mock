<?php

namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @author Incofab
 */
class ExamContent extends BaseModel
{
    use HasFactory, QueryInstitution;

    protected $casts = [
        'institution_id' => 'integer',
    ];

    function courses()
    {
        return $this->hasMany(Course::class);
    }
}
