<?php

namespace App\Models;

use App\Enums\ExamStatus;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/***
 * @author Incofab
 */
class Exam extends BaseModel
{
  use HasFactory;

  protected $casts = [
    'event_id' => 'integer',
    'student_id' => 'integer',
    'num_of_questions' => 'integer',
    'score' => 'integer',
    'status' => ExamStatus::class,
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'pause_time' => 'datetime',
    'attempts' => AsArrayObject::class,
  ];

  static function generateExamNo()
  {
    $key = date('Y') . rand(10000000, 99999999);
    while (self::where('exam_no', '=', $key)->first()) {
      $key = date('Y') . rand(10000000, 99999999);
    }
    return $key;
  }

  function scorePercent()
  {
    return ($this->score /
      ($this->num_of_questions == 0 ? 1 : $this->num_of_questions)) *
      100;
  }

  function scorePercentSum()
  {
    return $this->examCourses->sum(fn($item) => $item->scorePercent());
    // return ($this->score /
    //   ($this->num_of_questions == 0 ? 1 : $this->num_of_questions)) *
    //   100;
  }

  function markAsStarted()
  {
    $this->fill([
      'start_time' => now(),
      'status' => ExamStatus::Active,
      'pause_time' => null,
      'end_time' => now()->addMinutes($this->event->duration),
    ])->save();
  }

  function markAsEnded($totalScore, $totalNumOfQuestions, $attempts = [])
  {
    $this->fill([
      'status' => ExamStatus::Ended,
      'score' => $totalScore,
      'num_of_questions' => $totalNumOfQuestions,
      'attempts' => $attempts,
    ])->save();
  }

  function markAsPaused()
  {
    $this->fill([
      'status' => ExamStatus::Paused,
      'pause_time' => now(),
      'start_time' => null,
      'end_time' => null,
    ])->save();
  }

  /** @return int the remaining time in seconds */
  function getTimeRemaining()
  {
    $timeRemaining = now()->diffInSeconds($this->end_time);
    return $timeRemaining < 1 ? 0 : $timeRemaining;
  }

  function isActive()
  {
    return $this->status === ExamStatus::Active;
  }
  function isPending()
  {
    return $this->status === ExamStatus::Pending;
  }
  function isEnded()
  {
    return $this->status === ExamStatus::Ended;
  }
  function isOngoing($examFileData)
  {
    $isEnded = ($examFileData['status'] ?? null) === ExamStatus::Ended->value;
    // info([$this->exam_no, $isEnded]);
    return !$isEnded && $this->status === ExamStatus::Active;
  }
  function canExtendTime()
  {
    return $this->status === ExamStatus::Active ||
      $this->status === ExamStatus::Ended;
  }

  function examCourses()
  {
    return $this->hasMany(ExamCourse::class);
  }

  function student()
  {
    return $this->belongsTo(Student::class);
  }

  function event()
  {
    return $this->belongsTo(Event::class);
  }

  function user()
  {
    return $this->belongsTo(User::class);
  }
}
