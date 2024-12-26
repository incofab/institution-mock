<?php
namespace Database\Factories;

use Faker\Generator as Faker;
use App\Models\ExamContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamContentFactory extends Factory
{
    function definition() {    
        $examname = ['WAEC', 'JAMB/UTME', 'NECO'];
        
        $examFullname = ['West African Examination Council', 'Universal Tertiary Matriculation Examination', 
        'National Examination Council'];
        
        $index = fake()->unique(false, 50)->randomKey($examname);
        
        return [
            'country' => fake()->country,  
            'exam_name' => $examname[$index], 
            'fullname' => $examFullname[$index], 
            'is_file_content_uploaded' => false,
            'description' => fake()->sentence,
        ];
        
    }
}
