<?php
namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSessionFactory extends Factory
{
    function definition()
    {
        $courseIDs = Course::all('id')->pluck('id')->toArray();
        $sessions = ['2001', '2002', '2003', '2004', '2005', '2006'];

        return [
            'course_id' => empty($courseIDs)
                ? Course::factory()
                : fake()->randomElement($courseIDs),
            'category' => '',
            'session' => fake()->randomElement($sessions),
        ];
    }
}
