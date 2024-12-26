<?php
namespace Database\Factories;

use App\Models\Event;
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
            'exam_no' => $this->faker->unique()->randomNumber(5, true),
            'time_remaining' => $this->faker->randomFloat(2, 0, 120),
            'start_time' => $this->faker->dateTime,
            'pause_time' => $this->faker->dateTime,
            'end_time' => $this->faker->dateTime,
            'score' => $this->faker->numberBetween(0, 100),
            'num_of_questions' => $this->faker->numberBetween(10, 100),
            'status' => 'active',
        ];
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
}
