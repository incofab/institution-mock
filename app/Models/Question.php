<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rule;

class Question extends BaseModel
{
  use HasFactory;
  protected $casts = [
    'course_session_id' => 'integer',
    'question_no' => 'integer',
  ];

  static function createRule(Question $question = null, $prefix = '')
  {
    $options = ['A', 'B', 'C', 'D', 'E'];
    return [
      $prefix . 'question_no' => ['required', 'integer'],
      $prefix . 'question' => ['required', 'string', 'max:60000'],
      $prefix . 'option_a' => ['required', 'max:60000'],
      $prefix . 'option_b' => ['required', 'max:60000'],
      $prefix . 'option_c' => ['nullable', 'max:60000'],
      $prefix . 'option_d' => ['nullable', 'max:60000'],
      $prefix . 'option_e' => ['nullable', 'max:60000'],
      $prefix . 'answer' => ['required', Rule::in($options)],
      $prefix . 'answer_meta' => ['nullable', 'string', 'max:60000'],
      $prefix . 'topic_id' => [
        'nullable',
        'integer',
        Rule::exists('topics', 'id'),
      ],
    ];
  }
  function courseSession()
  {
    return $this->belongsTo(CourseSession::class);
  }
}
