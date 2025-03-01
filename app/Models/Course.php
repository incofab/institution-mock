<?php

namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends BaseModel
{
  use HasFactory, QueryInstitution;
  protected $casts = [
    'institution_id' => 'integer',
    'exam_content_id' => 'integer',
  ];

  static function createRule()
  {
    return [
      'exam_content_id' => ['nullable', 'integer', 'exists:exam_contents,id'],
      'code' => ['required', 'string', 'max:255'],
      'title' => ['nullable', 'string', 'max:255'],
      'order' => ['nullable', 'string', 'max:255'],
    ];
  }

  public function canDelete()
  {
    return $this->courseSessions()->count();
  }

  function institution()
  {
    return $this->belongsTo(Institution::class);
  }

  function courseSessions()
  {
    return $this->hasMany(CourseSession::class);
  }

  function examContent()
  {
    return $this->belongsTo(ExamContent::class);
  }
}
