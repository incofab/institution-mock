<?php

namespace App\Models;

use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends BaseModel
{
  use HasFactory, QueryInstitution;

  protected $casts = [
    'institution_id' => 'integer',
    'duration' => 'integer',
    'external_content_id' => 'integer',
    // 'external_event_courses' => AsArrayObject::class,
  ];

  static function ruleCreate(Event $event = null)
  {
    return [
      'title' => ['required', 'string'],
      'description' => ['nullable', 'string'],
      'duration' => ['required', 'integer'],
      ...$event
        ? []
        : [
          'external_content_id' => ['sometimes', 'exists:external_contents,id'],
        ],
    ];
  }

  function scopeActive($query)
  {
    return $query->where('status', 'active');
  }

  function isExternal()
  {
    return $this->external_content_id;
  }

  function isNotExternal()
  {
    return !$this->external_content_id;
  }

  function getEventCourses()
  {
    if (!$this->isExternal()) {
      return $this->eventCourses;
    }
    return $this->external_event_courses;
  }

  function externalEventCourses(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return collect($valueArr)->map(function ($item) {
          $eventCourse = new EventCourse($item);
          $courseSession = new CourseSession($item['course_session'] ?? []);
          $courseSession->course = new Course(
            $item['course_session']['course'] ?? [],
          );
          $eventCourse->course_session = $courseSession;
          return $eventCourse;
        });
      },
      set: fn($value) => json_encode($value),
    );
  }

  function institution()
  {
    return $this->belongsTo(Institution::class);
  }

  function eventCourses()
  {
    return $this->hasMany(EventCourse::class);
  }

  function exams()
  {
    return $this->hasMany(Exam::class);
  }
  function externalContent()
  {
    return $this->belongsTo(ExternalContent::class);
  }
}
