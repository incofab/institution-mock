<?php

namespace App\Models;

use App\Actions\PullEventCourseContent;
use App\Traits\QueryInstitution;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
/**
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\EventCourse> $eventCourses
 */
class Event extends BaseModel
{
  use HasFactory, QueryInstitution;

  protected $casts = [
    'institution_id' => 'integer',
    'duration' => 'integer',
    'external_content_id' => 'integer',
    // 'external_event_courses' => AsArrayObject::class,
  ];

  static function ruleCreate(Event|null $event = null)
  {
    return [
      'title' => ['required', 'string'],
      'description' => ['nullable', 'string'],
      'duration' => ['required', 'integer'],
      ...$event
        ? []
        : [
          'external_content_id' => ['nullable', 'exists:external_contents,id'],
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
  function findCourseSession($courseSessionId): CourseSession|array|null
  {
    return $this->getEventCourses()
      ->filter(
        fn($item) => $item['course_session_id'] == intval($courseSessionId),
      )
      ->first()
      ?->getCourseSession();
  }

  function externalEventCourses(): Attribute
  {
    return Attribute::make(
      get: function ($value) {
        $valueArr = json_decode($value, true) ?? [];
        return collect($valueArr)->map(function ($item) {
          $eventCourse = new EventCourse($item);
          $courseSession = CourseSession::buildCourseSession(
            $item['course_session'] ?? [],
          );
          // $questions = collect($item['course_session']['questions'] ?? [])->map(
          //   fn($q) => new Question($q),
          // );
          // $courseSession = new CourseSession($item['course_session'] ?? []);
          // $courseSession['questions'] = $questions;
          // $courseSession['course'] = new Course(
          //   $item['course_session']['course'] ?? [],
          // );
          $eventCourse->course_session = $courseSession;
          $eventCourse->courseSession = $courseSession;
          return $eventCourse;
        });
      },
      set: fn($value) => json_encode($value),
    );
  }

  function loadContent()
  {
    if ($this->isExternal()) {
      (new PullEventCourseContent($this))->mapEventCourseContent();
    } else {
      $this->eventCourses = EventCourse::query()
        ->where('event_id', $this->id)
        ->with(
          'courseSession.course',
          'courseSession.questions',
          'courseSession.instructions',
          'courseSession.passages',
        )
        ->get();
    }
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
