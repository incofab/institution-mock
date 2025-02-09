<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\ExamContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamContentFactory extends Factory
{
  function definition()
  {
    $examname = ['WAEC', 'JAMB/UTME', 'NECO'];

    $examFullname = [
      'West African Examination Council',
      'Universal Tertiary Matriculation Examination',
      'National Examination Council',
    ];

    $index = fake()->unique(false, 50)->randomKey($examname);

    return [
      // 'country' => fake()->country,
      'exam_name' => $examname[$index],
      'fullname' => $examFullname[$index],
      'is_file_content_uploaded' => false,
      'description' => fake()->sentence,
    ];
  }

  function courses($count = 5, $sessionCount = 5)
  {
    return $this->afterCreating(
      fn(ExamContent $examContent) => Course::factory($count)
        ->for($examContent)
        ->courseSessions($sessionCount),
    );
  }
}
