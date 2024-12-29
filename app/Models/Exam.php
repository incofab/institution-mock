<?php

namespace App\Models;

use App\Enums\ExamStatus;
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
  ];

  static function generateExamNo()
  {
    $key = date('Y') . rand(10000000, 99999999);
    while (self::where('exam_no', '=', $key)->first()) {
      $key = date('Y') . rand(10000000, 99999999);
    }
    return $key;
  }

  /** @return int the remaining time in seconds */
  function getTimeRemaining()
  {
    $timeRemaining = now()->diffInSeconds($this->end_time);
    return $timeRemaining < 1 ? 0 : $timeRemaining;
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
