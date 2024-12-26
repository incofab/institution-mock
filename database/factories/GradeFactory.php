<?php
namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    function definition()
    {
        return [
            'institution_id' => Institution::factory(),
            'title' => $this->faker->word,
            'description' => $this->faker->paragraph,
        ];
    }
}
