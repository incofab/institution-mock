<?php
namespace App\Actions;

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
      ->map(fn($item) => ['course_session_id' => $item['course_session_id']]);
    try {
      $courseSessions = Cache::get($this->getCacheKey(), function () use (
        $courseSessionIds,
      ) {
        $res = Http::post(
          'http://content.examscholars.com/api/course-sessions/retrieve',
          ['subjects' => $courseSessionIds],
        );

        $body = $res->body();
        if (!$res->ok()) {
          info('Error syncing courses: ' . $body);
          return [];
        }

        $courseSessions = $res->json('course_sessions');
        $this->cacheContent($courseSessions);
        return $courseSessions;
      });
      //   foreach ($courseSessions as $key => $cs) {
      //     info("Available course sessions = {$cs['id']}");
      //   }
      return $courseSessions;
    } catch (\Throwable $th) {
      return [];
    }
  }

  private function cacheContent(array $content)
  {
    Cache::put($this->getCacheKey(), $content, now()->addHours(12));
  }
}
