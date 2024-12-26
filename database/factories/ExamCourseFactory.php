<?php
namespace Database\Factories;

use App\Models\CourseSession;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamCourseFactory extends Factory
{
    function definition()
    {
        return [
            'exam_id' => Exam::factory(),
            'course_session_id' => CourseSession::factory()->create()->id,
            'score' => $this->faker->numberBetween(0, 100),
            'num_of_questions' => $this->faker->numberBetween(10, 100),
            'status' => 'active',
        ];
    }
}
