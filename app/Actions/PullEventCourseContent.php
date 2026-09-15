<?php
namespace App\Actions;

use App\Models\CourseSession;
use App\Models\Event;
use Http;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\ValidationException;

class PullEventCourseContent
{
  function __construct(private Event $event)
  {
  }

  function getEventCourseContent()
  {
    return $this->retrieveEventCourse(
      $this->event
        ->getEventCourses()
        ->map(fn($item) => ['course_session_id' => $item['course_session_id']])
        ->values()
        ->all(),
    );
  }

  function retrieveEventCourse($courseSessionIds)
  {
    if (empty($courseSessionIds)) {
      return [];
    }

    try {
      $res = Http::acceptJson()
        ->timeout(60)
        ->post(
          'https://content.examscholars.com/api/course-sessions/retrieve',
          ['subjects' => $courseSessionIds],
        );
    } catch (ConnectionException $exception) {
      throw ValidationException::withMessages([
        'content' => 'Course content service is unavailable. Please try again.',
      ]);
    }

    $sessions = $res->json('course_sessions');
    $validator = validator(
      ['course_sessions' => $sessions],
      [
        'course_sessions' => ['required', 'array'],
        'course_sessions.*' => ['required', 'array'],
        'course_sessions.*.id' => ['required', 'integer', 'distinct'],
        'course_sessions.*.course' => ['required', 'array'],
        'course_sessions.*.questions' => ['required', 'array', 'min:1'],
        'course_sessions.*.questions.*' => ['required', 'array'],
        'course_sessions.*.questions.*.id' => ['required', 'integer'],
        'course_sessions.*.instructions' => ['present', 'array'],
        'course_sessions.*.instructions.*' => ['required', 'array'],
        'course_sessions.*.passages' => ['present', 'array'],
        'course_sessions.*.passages.*' => ['required', 'array'],
      ],
    );
    if (!$res->successful() || $validator->fails()) {
      throw ValidationException::withMessages([
        'content' => 'Course content could not be retrieved. Please try again.',
      ]);
    }
    return $sessions;
  }

  function mapEventCourseContent()
  {
    $sessions = collect($this->getEventCourseContent())->keyBy('id');
    $courses = $this->event
      ->getEventCourses()
      ->map(function ($eventCourse) use ($sessions) {
        $session = $sessions->get($eventCourse->course_session_id);
        if (!$session) {
          throw ValidationException::withMessages([
            'content' => "Course session {$eventCourse->course_session_id} is unavailable.",
          ]);
        }
        $eventCourse->course_session = CourseSession::buildCourseSession(
          $session,
        );
        return $eventCourse;
      });
    $this->event->external_event_courses = $courses->toArray();
  }
}
