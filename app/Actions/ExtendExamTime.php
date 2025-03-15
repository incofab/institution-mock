<?php
namespace App\Actions;

use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Exam;

class ExtendExamTime
{
  function __construct(private Exam $exam)
  {
  }

  static function make(Exam $exam)
  {
    return new self($exam);
  }

  function run($duration)
  {
    $now = now();
    $endTime = $now->greaterThan($this->exam->end_time)
      ? $now
      : $this->exam->end_time;

    $this->exam
      ->fill([
        'status' => ExamStatus::Active,
        'pause_time' => null,
        'end_time' => $endTime->addMinutes($duration),
      ])
      ->save();

    $examHandler = ExamHandler::make();
    $ret = $examHandler->syncExamFile($this->exam, false);
    return $ret->isSuccessful()
      ? successRes($ret->getMessage())
      : failRes($ret->getMessage());
  }
}
