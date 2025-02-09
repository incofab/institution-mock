<?php
namespace App\Actions;

use App\Models\CourseSession;
use App\Models\Event;
use App\Models\Exam;
use Illuminate\Validation\ValidationException;

class RegisterExam
{
  function __construct(private Event $event)
  {
  }

  function run($studentId, array $courseSessionIds, $isMulti = false)
  {
    $existingExam = $this->event
      ->exams()
      ->where('student_id', $studentId)
      ->exists();

    if ($existingExam) {
      if ($isMulti) {
        return;
      }
      throw ValidationException::withMessages([
        'student_id' => 'This Student already has an exam for this event',
      ]);
    }

    $exam = $this->event->exams()->firstOrCreate(
      [
        'student_id' => $studentId,
        'institution_id' => $this->event->institution_id,
      ],
      [
        'time_remaining' => $this->event->duration,
        'exam_no' => Exam::generateExamNo(),
      ],
    );

    foreach ($courseSessionIds as $key => $id) {
      $courseSession = $this->getCourseSession($id);
      $exam->examCourses()->firstOrCreate([
        'course_session_id' => $id,
        'course_code' => $courseSession->course->course_code,
        'session' => $courseSession->session,
      ]);
    }
  }
  private function getCourseSession($courseSessionId): CourseSession
  {
    $eventCourse = $this->event
      ->getEventCourses()
      ->where('course_session_id', $courseSessionId)
      ->first();
    return $eventCourse->courseSession;
  }
}
