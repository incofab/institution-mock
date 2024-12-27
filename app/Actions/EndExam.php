<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Question;
use App\Support\Res;

class EndExam
{
  private ExamHandler $examHandler;
  function __construct(private $examNo, private $studentCode)
  {
    $this->examHandler = new ExamHandler();
  }

  function endEventExams(Event $event)
  {
    $exams = $event->exams()->with('examCourses')->get();
    foreach ($exams as $exam) {
      $this->endExam($exam);
    }
  }

  function endExam(Exam $exam): Res
  {
    $examCourses = $exam->examCourses;

    if ($exam->status === ExamStatus::Ended) {
      return failRes('Exam already submitted');
    }

    $totalScore = 0;
    $totalNumOfQuestions = 0;

    /** @var \App\Models\ExamCourse $examCourse */
    foreach ($examCourses as $examCourse) {
      $questions = Question::where(
        'course_session_id',
        $examCourse->course_session_id,
      )->get(['id', 'answer']);

      $scoreDetail = $this->examHandler->calculateScoreFromFile(
        $exam,
        $questions,
      );

      $score = $scoreDetail['score'];
      $numOfQuestions = $scoreDetail['num_of_questions'];
      $examCourse
        ->fill([
          'score' => $score,
          'num_of_questions' => $numOfQuestions,
          'status' => ExamStatus::Ended->value,
        ])
        ->save();
      $totalScore += $score;
      $totalNumOfQuestions += $numOfQuestions;
      //   dlog("totalScore = $totalScore");
    }
    ExamHelper::make()->endExam($exam, $totalScore, $totalNumOfQuestions);
    $this->examHandler->syncExamFile($exam);

    return successRes('Exam ended');
  }
}
