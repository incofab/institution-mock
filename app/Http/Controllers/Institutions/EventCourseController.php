<?php
namespace App\Http\Controllers\Institutions;

use App\Models\Course;
use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Models\EventCourse;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EventCourseController extends Controller
{
  function index(Institution $institution, Event $event)
  {
    $event->load('externalContent', 'eventCourses.courseSession.course');
    return view('institutions.event-courses.index', [
      'allRecords' => $event->getEventCourses(),
      'event' => $event,
      'courses' => $event->isExternal()
        ? $event->externalContent->exam_content->courses
        : Course::query()
          ->with('courseSessions', fn($q) => $q->latest('session'))
          ->oldest('order')
          ->get(),
    ]);
  }

  function store(Request $request, Institution $institution, Event $event)
  {
    $data = $request->validate([
      'course_id' => ['required', 'integer'],
      'course_session_id' => [
        'required',
        'integer',
        Rule::when($event->isNotExternal(), 'exists:course_sessions,id'),
      ],
      'num_of_questions' => ['nullable', 'integer'],
    ]);

    if ($event->isExternal()) {
      /** @var \App\Models\ExternalContent $externalContent */
      $externalContent = $event->externalContent;
      $eventCourse = $externalContent->makeEventCourse(
        $event,
        $data['course_id'],
        $data['course_session_id'],
      );
      // dd($eventCourse?->toArray());
      // dd(collect($event->getEventCourses())->push($eventCourse)->toArray());
      $event
        ->fill([
          'external_event_courses' => collect($event->getEventCourses())
            ->push($eventCourse)
            ->toArray(),
        ])
        ->save();
    } else {
      $event
        ->eventCourses()
        ->updateOrCreate(
          ['course_session_id' => $data['course_session_id']],
          collect($data)->except('course_id')->toArray(),
        );
    }

    return redirect(instRoute('event-courses.index', $event))->with(
      'message',
      'Event course added',
    );
  }

  function multiCreate(Institution $institution, Event $event)
  {
    $courses = $event->isExternal()
      ? $event->externalContent->exam_content->courses
      : Course::query()
        ->when(
          $event->exam_content_id,
          fn($q, $value) => $q->where('exam_content_id', $value),
        )
        ->with('courseSessions', fn($q) => $q->latest('session'))
        ->oldest('order')
        ->oldest('course_code')
        ->get();
    return view('institutions.event-courses.create-multi-event-courses', [
      'event' => $event,
      'courses' => $courses,
    ]);
  }

  function multiStore(Request $request, Institution $institution, Event $event)
  {
    $validatedData = $request->validate([
      'subjects' => ['required', 'array', 'min:1'],
      'subjects.*.num_of_questions' => ['nullable', 'integer'],
      'subjects.*.course_id' => ['required', 'integer'],
      'subjects.*.course_session_id' => [
        'nullable',
        'integer',
        // 'exists:course_sessions,id',
        Rule::when($event->isNotExternal(), 'exists:course_sessions,id'),
      ],
    ]);
    $filteredSubjects = array_filter(
      $validatedData['subjects'],
      fn($item) => !empty($item['course_session_id']),
    );
    if ($event->isExternal()) {
      /** @var \App\Models\ExternalContent $externalContent */
      $externalContent = $event->externalContent;
      $eventCourses = collect($filteredSubjects)->map(
        fn($item) => $externalContent->makeEventCourse(
          $event,
          $item['course_id'],
          $item['course_session_id'],
        ),
      );
      $event->fill(['external_event_courses' => $eventCourses])->save();
    } else {
      foreach ($filteredSubjects as $key => $data) {
        $event
          ->eventCourses()
          ->updateOrCreate(
            ['course_session_id' => $data['course_session_id']],
            collect($data)->except('course_id')->toArray(),
          );
      }
    }
    return redirect(instRoute('event-courses.index', $event))->with(
      'message',
      'Multiple event courses added',
    );
  }

  function destroy(Institution $institution, EventCourse $eventCourse)
  {
    $eventCourse->delete();
    return redirect(
      instRoute('event-courses.index', [$eventCourse->event_id]),
    )->with('message', 'Event course deleted');
  }
}
