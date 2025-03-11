<?php

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\deleteJson;

beforeEach(function () {
  $this->institution = Institution::factory()->user()->create();
  $this->assignedUser = $this->institution->institutionUsers()->first()->user;
  $this->event = Event::factory()
    ->institution($this->institution)
    ->create();
  actingAs($this->assignedUser);
});

it('displays a list of exams for an event', function () {
  Exam::factory()
    ->count(3)
    ->event($this->event)
    ->create();

  $response = $this->get(
    route('institutions.exams.index', [$this->institution, $this->event]),
  );

  $response->assertOk();
  $response->assertViewIs('institutions.exams.index');
  $response->assertViewHas('allRecords');
  $response->assertViewHas('event', $this->event);
});

it('displays the create exam view with relevant data', function () {
  $student = Student::factory()
    ->institution($this->institution)
    ->create();
  $events = Event::factory()
    ->count(2)
    ->institution($this->institution)
    ->active()
    ->create();

  $response = $this->get(
    route('institutions.exams.create', [$this->institution, $student]),
  );

  $response->assertOk();
  $response->assertViewIs('institutions.exams.create');
  $response->assertViewHasAll(['events', 'students', 'student', 'event']);
});

it('stores a new exam successfully', function () {
  //   $this->institution = Institution::factory()->create();
  //   $event = Event::factory()->create();
  $student = Student::factory()
    ->institution($this->institution)
    ->create();
  $course = Course::factory()
    ->institution($this->institution)
    ->create();
  $courseSessions = CourseSession::factory(2)->course($course)->create();
  $eventCourses = $this->event
    ->eventCourses()
    ->createMany([
      ['course_session_id' => $courseSessions[0]->id],
      ['course_session_id' => $courseSessions[1]->id],
    ]);

  $data = [
    'event_id' => $this->event->id,
    'student_id' => $student->id,
    'course_session_ids' => $eventCourses
      ->pluck('course_session_id')
      ->toArray(),
  ];

  $this->post(
    route('institutions.exams.store', [$this->institution]),
    $data,
  )->assertRedirect();
});

it('displays the create-grade-exam view with event data', function () {
  $response = $this->get(
    route('institutions.exams.events.grades.create', [
      $this->institution,
      $this->event,
    ]),
  );

  $response->assertOk();
  $response->assertViewIs('institutions.exams.create-grade-exam');
  $response->assertViewHasAll(['event', 'eventCourses', 'grades']);
});

it('stores grade exams for multiple students', function () {
  $grade = Grade::factory()
    ->for($this->institution)
    ->create();
  Student::factory()->count(3)->grade($grade)->create();
  $course = Course::factory()
    ->institution($this->institution)
    ->create();
  [$courseSession1, $courseSession2] = CourseSession::factory(2)
    ->course($course)
    ->create();
  $eventCourses = $this->event
    ->eventCourses()
    ->createMany([
      ['course_session_id' => $courseSession1->id],
      ['course_session_id' => $courseSession2->id],
    ]);

  $data = [
    'grade_id' => $grade->id,
    'course_session_ids' => $eventCourses
      ->pluck('course_session_id')
      ->toArray(),
  ];

  $this->post(
    route('institutions.exams.events.grades.store', [
      $this->institution,
      $this->event,
    ]),
    $data,
  )->assertRedirect();
});

it('stores multiple exams for different students', function () {
  $students = Student::factory()
    ->count(2)
    ->institution($this->institution)
    ->create();
  $course = Course::factory()
    ->institution($this->institution)
    ->create();
  [$courseSession1, $courseSession2] = CourseSession::factory(2)
    ->course($course)
    ->create();
  $eventCourses = $this->event
    ->eventCourses()
    ->createMany([
      ['course_session_id' => $courseSession1->id],
      ['course_session_id' => $courseSession2->id],
    ]);

  $data = [
    'items' => [
      [
        'student_id' => $students[0]->id,
        'course_session_ids' => $eventCourses
          ->pluck('course_session_id')
          ->toArray(),
      ],
      [
        'student_id' => $students[1]->id,
        'course_session_ids' => $eventCourses
          ->pluck('course_session_id')
          ->toArray(),
      ],
    ],
  ];

  $this->post(
    route('institutions.exams.multi-store-exam', [
      $this->institution,
      $this->event,
    ]),
    $data,
  )->assertRedirect();
});

it('deletes an exam successfully', function () {
  $exam = Exam::factory()
    ->event($this->event)
    ->create();

  $response = deleteJson(
    route('institutions.exams.destroy', [$this->institution, $exam]),
  );

  $response->assertRedirect();
  $response->assertSessionHas('message', 'Exam deleted');
});
