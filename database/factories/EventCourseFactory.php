<?php
namespace Database\Factories;

use App\Models\CourseSession;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventCourseFactory extends Factory
{
    function definition()
    {
        return [
            'event_id' => Event::factory(),
            'course_session_id' => CourseSession::factory()->create()->id,
            'status' => 'active',
        ];
    }
}
