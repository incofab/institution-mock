<?php
namespace Database\Seeders;

use App\Models\Event;
use App\Models\Institution;
use Http;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $this->singleTestEvent();

    // $institution = Institution::factory()->create();
    // Event::factory(3)
    //   ->institution($institution)
    //   ->eventCourses(6)
    //   ->create();
  }
  function multipleTestEvents()
  {
    $institution = Institution::factory()->create();
    $events = Event::factory(3)
      ->institution($institution)
      ->eventCourses(6)
      ->create();
    $res = Http::post(
      route('api.institutions.events.index', $institution),
    )->json('data');
    info(json_encode($res, JSON_PRETTY_PRINT));
    $events->each(function ($event) {
      $event->delete();
    });
  }

  function singleTestEvent()
  {
    $institution = Institution::factory()->create();
    $event = Event::factory()
      ->institution($institution)
      ->eventCourses(6, 20)
      ->create();
    $res = Http::post(
      route('api.institutions.events.show', [$institution, $event]),
    )->json('data');
    info(json_encode($res, JSON_PRETTY_PRINT));
    $event->delete();
  }
}
