<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamCourse extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'course_session_id' => 'integer',
        'exam_id' => 'integer',
        'score' => 'integer',
        'num_of_questions' => 'integer',
    ];

    static function ruleCreate()
    {
        return [
            //             'exam_no' => ['required', 'string'],
            'num_of_questions' => ['required', 'numeric', 'min:1'],
            'course_id' => ['required'],
            'course_session_id' => ['required'],
        ];
    }

    // const STATUSES = ['active', 'ended'];

    function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }
}
