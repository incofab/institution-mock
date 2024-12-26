<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instruction extends BaseModel
{
    use HasFactory;
    protected $casts = [
        'course_session_id' => 'integer',
    ];

    function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }
}
