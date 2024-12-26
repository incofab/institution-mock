<?php
namespace Database\Factories;

use App\Models\Grade;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    function definition()
    {
        return [
            'institution_id' => Institution::factory(),
            'grade_id' => Grade::factory(),
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'code' => $this->faker->unique()->randomNumber(5, true),
        ];
    }

    function institution(Institution $institution)
    {
        return $this->state(
            fn(array $attr) => [
                'institution_id' => $institution,
                'grade_id' => Grade::factory()->for($institution),
            ],
        );
    }
}
