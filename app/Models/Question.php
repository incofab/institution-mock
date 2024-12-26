<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Question extends BaseModel
{
    use HasFactory;
    protected $casts = [
        'course_session_id' => 'integer',
        'question_no' => 'integer',
    ];

    static function ruleCreate()
    {
        return [
            'course_session_id' => ['required', 'numeric'],
            'question_no' => ['required', 'numeric'],
            'question' => ['required', 'string'],
            'option_a' => ['required', 'string'],
            'option_b' => ['required', 'string'],
            'option_c' => ['required', 'string'],
            'answer' => ['required', 'string'],
        ];
    }

    function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }
}
