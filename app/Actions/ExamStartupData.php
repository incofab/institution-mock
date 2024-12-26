<?php
namespace App\Actions;

use App\Helpers\ExamHandler;
use App\Models\Exam;

class ExamStartupData
{
  function __construct(private $examNo, private $studentCode)
  {
  }

  function run()
  {
    $exam = Exam::query()
      ->where('exam_no', $this->examNo)
      ->with(
        'examCourses.courseSession.course',
        'student',
        'examCourses.courseSession.questions',
      )
      ->firstOrFail();

    if ($this->studentCode && $exam->student->code === $this->studentCode) {
      return failRes('This exam does not belong to the supplied student');
    }

    $examHandler = new ExamHandler();
    $ret = $examHandler->syncExamFile($exam);
    if (!$ret['success']) {
      return failRes($ret['message']);
    }

    $ret = $examHandler->getContent($exam->event_id, $this->examNo);

    if (empty($ret['content'])) {
      $ret = failRes($ret['message']);
    }
    return successRes('', [
      'exam' => $exam,
      'exam_track' => $ret['content'],
    ]);
  }
}
