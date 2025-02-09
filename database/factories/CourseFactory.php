<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\ExamContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
  function definition()
  {
    $courseCodes = [
      'Engish',
      'Maths',
      'Economics',
      'Biology',
      'CRS',
      'Agric',
      'Commerce',
      'Government',
      'Social Studies',
      'Igb',
      'Hausa',
      'Yoruba',
      'IRK',
      'Geography',
    ];

    // $examContentIDs = \App\Models\ExamContent::all('id')->pluck('id')->toArray();

    return [
      'institution_id' => null, //Institution::factory(),
      'course_code' => fake()->randomElement($courseCodes),
      // 'exam_content_id' => ExamContent::factory(),
      'category' => fake()->word,
      'course_title' => fake()->words(7, true),
      'description' => fake()->sentence,
      'is_file_content_uploaded' => false,
    ];
  }

  function courseSessions($sessionCount = 5)
  {
    return $this->afterCreating(
      fn(Course $course) => CourseSession::factory($sessionCount)->for($course),
    );
  }
}
