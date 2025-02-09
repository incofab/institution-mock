<?php
namespace Database\Factories;

use App\Models\ExamContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExternalContentFactory extends Factory
{
  function definition()
  {
    return [
      'name' => fake()->word,
      'content_id' => fake()->randomDigitNotZero(),
      'source' => fake()->word,
    ];
  }

  function examContent($coursesCount = 5)
  {
    return $this->state(
      fn($attr) => [
        'exam_content' => ExamContent::factory()
          ->courses($coursesCount)
          ->make()
          ->toArray(),
      ],
    );
  }
}
