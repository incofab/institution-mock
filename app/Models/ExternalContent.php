<?php

namespace App\Models;

use App\Enums\ContentSource;
use App\Traits\ExternalContentActions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property Collection<int, \App\Models\ExamContent> $exam_content
 * @author Incofab
 */
class ExternalContent extends BaseModel
{
  use HasFactory, ExternalContentActions;

  protected $casts = [
    'content_id' => 'integer',
    'source' => ContentSource::class,
  ];

  function examContent(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        $examContent = new ExamContent($valueArr);
        $examContent['courses'] = collect($valueArr['courses'] ?? [])->map(
          function ($courseData) {
            $courseSessions = $courseData['course_sessions'] ?? [];
            $course = new Course($courseData);
            $course->course_sessions = collect($courseSessions)->map(
              fn($courseSession) => new CourseSession($courseSession),
            );
            return $course;
          },
        );
        return $examContent;
      },
      set: fn($value) => json_encode($value),
    );
  }
}
