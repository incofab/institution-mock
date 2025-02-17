<?php
namespace Database\Seeders;

use App\Models\Event;
use App\Models\Institution;
use Http;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // $examCourses = ExamCourse::factory(2)
    //   ->exam(Exam::factory()->create())
    //   ->courseSession()
    //   ->create();
    // dd($examCourses);

    $this->testExam();
    // Exam::factory(2)->notStarted()->create();
  }

  function testExam()
  {
    $institution = Institution::factory()->create();
    $event = Event::factory()
      ->institution($institution)
      ->eventCourses(6, 20)
      ->exams(10)
      ->create();
    $res = Http::post(
      route('api.institutions.events.exams.index', [$institution, $event]),
    )->json('data');
    info(json_encode($res, JSON_PRETTY_PRINT));

    $event->delete();
  }
}
