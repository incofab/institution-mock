<?php
namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\ExamContent;
use Illuminate\Database\Seeder;
use App\Models\ExternalContent;

class ExternalContentSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $examContent = ExamContent::factory()->create();
    $examContent->courses = Course::factory(5)
      ->for($examContent)
      ->create()
      ->map(function ($course) {
        $course->course_sessions = CourseSession::factory(5)
          ->for($course)
          ->create()
          ->toArray();
        return $course;
      })
      ->toArray();
    // dd($examContent->toArray());
    ExternalContent::factory()->create([
      'exam_content' => $examContent->toArray(),
    ]);
    $examContent->courses()->get()->map(fn($course) => $course->delete());
    $examContent->delete();
  }
}
