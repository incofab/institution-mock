<?php

namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends BaseModel
{
    use HasFactory, QueryInstitution;

    protected $casts = [
        'institution_id' => 'integer',
        'duration' => 'integer',
    ];

    static function ruleCreate()
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'duration' => ['required', 'integer'],
        ];
    }

    function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    function eventCourses()
    {
        return $this->hasMany(EventCourse::class);
    }

    function exams()
    {
        return $this->hasMany(Exam::class);
    }
}
