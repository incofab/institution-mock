<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Institution;

class EventController extends Controller
{
  function index(Institution $institution, Request $request)
  {
    $latestEventId = $request->latest_event_id ?? 0;
    $events = Event::query()
      ->where('id', '>', $latestEventId)
      ->with('eventCourses.courseSession.course')
      ->latest('id')
      ->take(100)
      ->get()
      ->map(function ($event) {
        $event->event_courses = $event->getEventCourses();
        return $event;
      });

    return $this->apiSuccessRes($events);
  }

  function show(Institution $institution, Event $event)
  {
    $event->load('eventCourses.courseSession.course');
    return $this->apiSuccessRes($event);
  }

  function deepShow(Institution $institution, Event $event)
  {
    //Todo: If event is external, call the external API to retrieve Event Course
    if ($event->isExternal()) {
      return $this->apiFailRes('External event content not available');
    } else {
      $event->load(
        'eventCourses.courseSession.course',
        'eventCourses.courseSession.passages',
        'eventCourses.courseSession.instructions',
        'eventCourses.courseSession.questions',
      );
    }
    return $this->apiSuccessRes($event);
  }
}
