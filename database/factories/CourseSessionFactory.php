<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseSessionFactory extends Factory
{
  function definition()
  {
    $sessions = ['2001', '2002', '2003', '2004', '2005', '2006'];
    return [
      'course_id' => Course::factory(),
      'category' => '',
      'session' => fake()->randomElement($sessions),
    ];
  }

  function questions($count = 10)
  {
    return $this->afterCreating(function (CourseSession $model) use ($count) {
      Question::factory($count)->courseSession($model)->create();
    });
  }
}
