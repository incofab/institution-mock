<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Exam;

class ExamHelper
{
  function __construct()
  {
  }

  static function make()
  {
    return new self();
  }

  function getExamStartupData($examNo, $studentCode, $start = true)
  {
    $exam = Exam::query()
      ->where('exam_no', $examNo)
      ->with(
        'examCourses.courseSession.course',
        'student',
        'examCourses.courseSession.questions',
        'event',
      )
      ->firstOrFail();

    if ($studentCode && $exam->student->code === $studentCode) {
      return failRes('This exam does not belong to the supplied student');
    }

    if ($start && $this->canStartExam($exam)) {
      $this->startExam($exam);
    }

    $examHandler = new ExamHandler();
    $ret = $examHandler->syncExamFile($exam);
    if (!$ret['success']) {
      return failRes($ret['message']);
    }

    $ret = $examHandler->getContent($exam->event_id, $examNo);

    if (empty($ret['content'])) {
      $ret = failRes($ret['message']);
    }
    return successRes('', [
      'exam' => $exam,
      'exam_track' => $ret['content'],
    ]);
  }

  function canStartExam(Exam $exam)
  {
    return empty($exam->start_time) || !empty($exam->pause_time);
  }

  function startExam(Exam $exam)
  {
    if (!$this->canStartExam($exam)) {
      return;
    }
    $exam
      ->fill([
        'start_time' => now(),
        'status' => ExamStatus::Active,
        'pause_time' => null,
        'end_time' => now()->addMinutes($exam->event->duration),
      ])
      ->save();
  }

  function endExam(Exam $exam, $totalScore, $totalNumOfQuestions)
  {
    $exam
      ->fill([
        'status' => ExamStatus::Ended,
        'score' => $totalScore,
        'num_of_questions' => $totalNumOfQuestions,
      ])
      ->save();
  }
}
