<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EventCourse extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'institution_id' => 'integer',
        'event_id' => 'integer',
        'course_id' => 'integer',
    ];

    function event()
    {
        return $this->belongsTo(Event::class);
    }

    function courseSession()
    {
        return $this->belongsTo(CourseSession::class);
    }
}
