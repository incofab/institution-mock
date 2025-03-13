<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Question> $questions
 */
class CourseSession extends BaseModel
{
  use HasFactory;
  protected $casts = [
    'course_id' => 'integer',
    'exam_content_id' => 'integer',
  ];
  public $fillable = [
    'id',
    'course_id',
    'category',
    'session',
    'general_instructions',
    'file_path',
    'file_version',
  ];

  static function createRule($editUser = null)
  {
    return [
      'session' => ['required', 'string'],
      'category' => ['nullable', 'string'],
      'general_instructions' => ['nullable', 'string'],
    ];
  }

  static function buildCourseSession($data): self
  {
    $courseSession = new CourseSession($data ?? []);
    $courseSession->questions = collect($data['questions'] ?? [])->map(
      fn($item) => new Question($item),
    );
    $courseSession->instructions = collect($data['instructions'] ?? [])->map(
      fn($item) => new Instruction($item),
    );
    $courseSession->passages = collect($data['passages'] ?? [])->map(
      fn($item) => new Passage($item),
    );
    $courseSession->course = new Course($data['course'] ?? []);
    return $courseSession;
  }

  function course()
  {
    return $this->belongsTo(Course::class);
  }

  function questions()
  {
    return $this->hasMany(Question::class);
  }

  function instructions()
  {
    return $this->hasMany(Instruction::class);
  }

  function passages()
  {
    return $this->hasMany(Passage::class);
  }
}
