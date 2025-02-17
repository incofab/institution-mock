<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Event;
use App\Models\Exam;
use App\Support\Res;

class EndExam
{
  private ExamHandler $examHandler;
  function __construct()
  {
    $this->examHandler = new ExamHandler();
  }

  static function make()
  {
    return new self();
  }

  function endEventExams(Event $event)
  {
    $exams = $event
      ->exams()
      ->with('event.eventCourses.courseSession.questions')
      ->get();
    foreach ($exams as $exam) {
      $this->endExam($exam);
    }
  }

  function endExam(Exam $exam): Res
  {
    $examCourses = $exam->exam_courses;
    if ($exam->status === ExamStatus::Ended) {
      return failRes('Exam already submitted');
    }
    if ($exam->status !== ExamStatus::Active) {
      return failRes('Exam is not active');
    }

    $totalScore = 0;
    $totalNumOfQuestions = 0;
    /** @var \App\Models\ExamCourse $examCourse */
    foreach ($examCourses as $examCourse) {
      $questions =
        $exam->event->findCourseSession($examCourse->course_session_id)
          ?->questions ?? [];

      $scoreDetail = $this->examHandler->calculateScoreFromFile(
        $exam,
        $questions,
      );

      $score = $scoreDetail->getScore();
      $numOfQuestions = $scoreDetail->getNumOfQuestions();
      $examCourse->fill([
        'score' => $score,
        'num_of_questions' => $numOfQuestions,
        'status' => ExamStatus::Ended->value,
      ]);
      $totalScore += $score;
      $totalNumOfQuestions += $numOfQuestions;
    }
    $attempts =
      $this->examHandler->getContent($exam->exam_no)->getExamTrack()[
        'attempts'
      ] ?? [];
    $exam->markAsEnded($totalScore, $totalNumOfQuestions, $attempts);
    $this->examHandler->syncExamFile($exam, false);

    return successRes('Exam ended');
  }
}
