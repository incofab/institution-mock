<?php
namespace Database\Factories;

use Faker\Generator as Faker;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    function definition() {
        $couseSessionIDs = \App\Models\CourseSession::all('id')->pluck('id')->toArray();
        $topicIDs = \App\Models\Topic::all('id')->pluck('id')->toArray();
        
        return [
            'course_session_id' => fake()->randomElement($couseSessionIDs), 
            'topic_id' => fake()->randomElement($topicIDs), 
            'question_no' => rand(1,50), 
            'question' => fake()->paragraph,
            'option_a' => fake()->sentence, 
            'option_b' => fake()->sentence, 
            'option_c' => fake()->sentence, 
            'option_d' => fake()->sentence, 
            'option_e' => fake()->sentence, 
            'answer' => fake()->randomElement(['A', 'B', 'C', 'D']), 
            'answer_meta' => fake()->paragraph
        ];
    }
}
