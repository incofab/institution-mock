<?php
namespace Database\Factories;

use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Institution;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
  function definition()
  {
    return [
      'institution_id' => Institution::factory(),
      'event_id' => Event::factory(),
      'student_id' => Student::factory(),
      'exam_no' => $this->faker->unique()->numerify('#######'),
      'time_remaining' => $this->faker->randomFloat(2, 0, 120),
      'start_time' => $this->faker->dateTime,
      'pause_time' => $this->faker->dateTime,
      'end_time' => $this->faker->dateTime,
      'score' => $this->faker->numberBetween(0, 100),
      'num_of_questions' => $this->faker->numberBetween(10, 100),
      'status' => 'active',
    ];
  }

  function notStarted()
  {
    return $this->state(
      fn(array $attr) => [
        'start_time' => null,
        'pause_time' => null,
        'end_time' => null,
      ],
    );
  }

  function event(Event $event)
  {
    $institution = $event->institution;
    if (!$institution) {
      return $this->state(fn($attr) => []);
    }
    return $this->state(
      fn(array $attr) => [
        'institution_id' => $institution,
        'student_id' => Student::factory()->for($institution),
        'event_id' => $event,
      ],
    );
  }

  function institution(Institution $institution)
  {
    return $this->state(
      fn(array $attr) => [
        'institution_id' => $institution,
        'student_id' => Student::factory()->for($institution),
        'event_id' => Event::factory()->for($institution),
      ],
    );
  }

  function examCourses($count = 3)
  {
    return $this->afterCreating(
      fn(Exam $exam) => ExamCourse::factory($count)
        ->exam($exam)
        ->courseSession()
        ->create(),
    );
  }
}
