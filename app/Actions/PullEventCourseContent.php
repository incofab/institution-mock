<?php
namespace App\Actions;

use App\Models\CourseSession;
use App\Models\Event;
use Cache;
use Http;

class PullEventCourseContent
{
  function __construct(private Event $event)
  {
  }
  function getCacheKey()
  {
    return "event_{$this->event->id}";
  }

  function getEventCourseContent()
  {
    $courseSessionIds = $this->event
      ->getEventCourses()
      ->map(fn($item) => ['course_session_id' => $item['course_session_id']])
      ->toArray();
    try {
      $courseSessions = Cache::get($this->getCacheKey(), function () use (
        $courseSessionIds,
      ) {
        $res = Http::timeout(60)->post(
          'http://content.examscholars.com/api/course-sessions/retrieve',
          ['subjects' => $courseSessionIds],
        );
        $body = $res->body();
        if (!$res->ok()) {
          info('Error syncing courses: ' . $body);
          return [];
        }
        // info('content cached');
        $courseSessions = $res->json('course_sessions');
        $this->cacheContent($courseSessions);
        return $courseSessions;
      });
      //   foreach ($courseSessions as $key => $cs) {
      //     info("Available course sessions = {$cs['id']}");
      //   }
      return $courseSessions;
    } catch (\Throwable $th) {
      info('Error syncing courses: ' . $th->getMessage());
      return [];
    }
  }

  function mapEventCourseContent()
  {
    $eventCourseSessions = $this->getEventCourseContent();
    // Apply course session content to external event courses
    $this->event
      ->getEventCourses()
      ->each(function ($eventCourse) use ($eventCourseSessions) {
        $cs = array_filter(
          $eventCourseSessions,
          fn($item) => $item['id'] == $eventCourse['course_session_id'],
        );
        $courseSession = CourseSession::buildCourseSession(reset($cs));
        $eventCourse->course_session = $courseSession;
        $eventCourse->courseSession = $courseSession;
      });
  }

  private function cacheContent(array $content)
  {
    Cache::put($this->getCacheKey(), $content, now()->addHours(12));
  }
}
