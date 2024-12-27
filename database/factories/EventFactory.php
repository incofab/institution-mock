<?php
namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
  function definition()
  {
    return [
      'institution_id' => Institution::factory(),
      'title' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'duration' => $this->faker->numberBetween(20, 120),
      'status' => 'active',
    ];
  }

  function active()
  {
    return $this->state(fn($attr) => ['status' => 'active']);
  }
}
