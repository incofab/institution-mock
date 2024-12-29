<?php
namespace Database\Factories;

use App\Models\CourseSession;
use App\Models\Exam;
use App\Models\ExamCourse;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamCourseFactory extends Factory
{
  function definition()
  {
    return [
      'exam_id' => Exam::factory(),
      'course_session_id' => CourseSession::factory(),
      'score' => $this->faker->numberBetween(0, 100),
      'num_of_questions' => $this->faker->numberBetween(10, 100),
      'status' => 'active',
    ];
  }

  function exam(Exam $exam)
  {
    return $this->state(fn($attr) => ['exam_id' => $exam]);
  }

  function questions($count = 10)
  {
    return $this->afterCreating(function (ExamCourse $model) use ($count) {
      Question::factory($count)
        ->courseSession($model->courseSession)
        ->create();
    });
  }
}
