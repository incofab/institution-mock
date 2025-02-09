<?php
namespace App\Traits;

use App\Models\Event;
use App\Models\EventCourse;
use App\Models\ExamContent;

trait ExternalContentActions
{
  public function makeEventCourse(Event $event, $courseId, $courseSessionId)
  {
    /** @var ExamContent $content */
    $content = $this->exam_content;
    foreach ($content->courses as $course) {
      if ($course->id != $courseId) {
        continue;
      }
      foreach ($course->course_sessions as $courseSession) {
        if ($courseSession->id != $courseSessionId) {
          continue;
        }

        $courseClone = clone $course;
        $courseClone->course_sessions = [];
        $courseSession->course = $courseClone;
        return new EventCourse([
          'event_id' => $event->id,
          'course_session_id' => $courseSessionId,
          'num_of_questions' => $content->num_of_questions,
          'course_session' => $courseSession,
        ]);
      }
    }
    return null;
  }
}
