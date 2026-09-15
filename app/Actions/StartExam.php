<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Exam;
use App\Models\ExamCourse;

class StartExam
{
  function __construct(private Exam $exam)
  {
  }

  static function make(Exam $exam): self
  {
    return app()->make(StartExam::class, ['exam' => $exam]);
    // return new self($exam);
  }

  function getExamStartupData($start = true)
  {
    if ($this->shouldRestrictUnactivatedExamAccess()) {
      return failRes('This exam has not been activated yet.');
    }

    if ($this->exam->status === ExamStatus::Ended) {
      return failRes('Exam has already ended');
    }

    $this->exam->event->loadExamContent($this->exam->examCourses);

    if ($start && $this->canStartExam()) {
      $this->exam->markAsStarted();
    }

    $examHandler = ExamHandler::make();
    $ret = $examHandler->syncExamFile($this->exam);
    if ($ret->isNotSuccessful()) {
      return failRes($ret->getMessage());
    }

    $ret = $examHandler->getContent($this->exam->exam_no);

    if ($ret->isNotSuccessful() || empty($ret->getExamTrack())) {
      return failRes($ret->getMessage());
    }
    return successRes('', [
      'exam' => $this->prepareExam($this->exam),
      'exam_track' => $ret->getExamTrack(),
    ]);
  }

  private function prepareExam(Exam $exam)
  {
    $event = $exam->event;
    /** @var ExamCourse $examCourse */
    foreach ($exam->examCourses as $key => $examCourse) {
      $courseSession = $event->findCourseSession(
        $examCourse->course_session_id,
      );
      $courseSession['questions'] = collect($courseSession['questions'])->map(
        function ($item) {
          $item['answer'] = null;
          $item['answer_meta'] = null;
          return $item;
        },
      );
      $examCourse->course_session = $courseSession;
    }
    $event->external_event_courses = [];
    $event->event_courses = [];
    $event->eventCourses = [];
    // die(json_encode($event, JSON_PRETTY_PRINT));
    return $exam;
  }

  function canStartExam()
  {
    return in_array($this->exam->status, [
      ExamStatus::Pending,
      ExamStatus::Paused,
    ]);
  }

  private function shouldRestrictUnactivatedExamAccess(): bool
  {
    return config('exam.restrict_unactivated_exam_access_before_start') &&
      !$this->exam->isActivated();
  }
}
