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

  static function joinUpInstruction($post)
  {
    $arr = [];
    if (!isset($post['all_instruction'])) {
      return $arr;
    }

    $len = count(Arr::get($post['all_instruction'], 'instruction', []));

    for ($i = 0; $i < $len; $i++) {
      $arr[] = [
        'instruction' => $post['all_instruction']['instruction'][$i],
        'from' => $post['all_instruction']['from_'][$i],
        'to' => $post['all_instruction']['to_'][$i],
        'table_id' => Arr::get(Arr::get($post['all_instruction'], 'id'), $i),
      ];
    }
    return $arr;
  }

  static function joinUpPassage($post)
  {
    $arr = [];
    if (!isset($post['all_passages'])) {
      return $arr;
    }

    $len = count(Arr::get($post['all_passages'], 'passage', []));

    for ($i = 0; $i < $len; $i++) {
      $arr[] = [
        'passage' => $post['all_passages']['passage'][$i],
        'from' => $post['all_passages']['from_'][$i],
        'to' => $post['all_passages']['to_'][$i],
        'id' => Arr::get(Arr::get($post['all_passages'], 'id'), $i),
      ];
    }

    return $arr;
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
