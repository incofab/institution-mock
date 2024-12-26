<?php
namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grade extends BaseModel
{
    use HasFactory, QueryInstitution;

    protected $casts = [
        'institution_id' => 'integer',
    ];

    static function ruleCreate()
    {
        return [
            'institution_id' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }

    function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    function students()
    {
        return $this->hasMany(Student::class);
    }
}
