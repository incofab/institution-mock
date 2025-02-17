<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Institution;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSessionFactory extends Factory
{
  function definition()
  {
    $sessions = range(2001, 2025); //['2001', '2002', '2003', '2004', '2005', '2006'];
    return [
      'course_id' => Course::factory(),
      'category' => '',
      'session' => fake()->randomElement($sessions),
    ];
  }

  function questions($count = 10)
  {
    if ($count < 1) {
      return $this->state(fn($attr) => []);
    }
    return $this->afterCreating(function (CourseSession $model) use ($count) {
      Question::factory($count)->courseSession($model)->create();
    });
  }
  function institution(Institution $institution)
  {
    return $this->state(
      fn($attr) => [
        'course_id' => Course::factory()->institution($institution),
      ],
    );
  }
  function course(Course $course)
  {
    return $this->state(fn($attr) => ['course_id' => $course]);
  }
}
