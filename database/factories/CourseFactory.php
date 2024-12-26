<?php
namespace Database\Factories;

use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    function definition()
    {
        $courseCodes = ['Engish', 'Maths', 'Economics', 'Biology'];

        // $examContentIDs = \App\Models\ExamContent::all('id')->pluck('id')->toArray();

        return [
            'institution_id' => null, //Institution::factory(),
            'course_code' => fake()->randomElement($courseCodes),
            // 'exam_content_id' => fake()->randomElement($examContentIDs),
            'category' => fake()->word,
            'course_title' => fake()->words(7, true),
            'description' => fake()->sentence,
            'is_file_content_uploaded' => false,
        ];
    }
}
