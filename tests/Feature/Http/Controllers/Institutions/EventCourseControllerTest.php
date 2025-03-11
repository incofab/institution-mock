<?php

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Event;
use App\Models\EventCourse;
use App\Models\Institution;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\postJson;

beforeEach(function () {
  $this->institution = Institution::factory()->user()->create();
  $this->assignedUser = $this->institution->institutionUsers()->first()->user;
  $this->event = Event::factory()
    ->institution($this->institution)
    ->create();
  actingAs($this->assignedUser);
});

test('index method returns the correct view with data', function () {
  $eventCourse = EventCourse::factory()
    ->event($this->event)
    ->create();

  $response = $this->get(
    route('institutions.event-courses.index', [
      $this->institution,
      $this->event,
    ]),
  );

  $response
    ->assertStatus(200)
    ->assertViewIs('institutions.event-courses.index')
    ->assertViewHasAll([
      'allRecords' => function ($allRecords) use ($eventCourse) {
        return $allRecords->contains($eventCourse);
      },
      'event' => $this->event,
      'courses' => function ($courses) {
        return $courses->isNotEmpty();
      },
    ]);
});

test('store method validates and stores data correctly', function () {
  $course = Course::factory()
    ->institution($this->institution)
    ->create();
  $courseSession = CourseSession::factory()->course($course)->create();

  $data = [
    'course_id' => $course->id,
    'course_session_id' => $courseSession->id,
    'num_of_questions' => 10,
  ];

  $response = postJson(
    route('institutions.event-courses.store', [
      $this->institution,
      $this->event,
    ]),
    $data,
  );

  $response
    ->assertRedirect(
      route('institutions.event-courses.index', [
        $this->institution,
        $this->event,
      ]),
    )
    ->assertSessionHas('message', 'Event course added');

  $this->assertDatabaseHas('event_courses', [
    'course_session_id' => $courseSession->id,
    'num_of_questions' => 10,
  ]);
});

test('multiCreate method returns the correct view with data', function () {
  $course = Course::factory()
    ->institution($this->institution)
    ->create();

  $response = $this->get(
    route('institutions.event-courses.multi-create', [
      $this->institution,
      $this->event,
    ]),
  );

  $response
    ->assertStatus(200)
    ->assertViewIs('institutions.event-courses.create-multi-event-courses')
    ->assertViewHasAll([
      'event' => $this->event,
      'courses' => function ($courses) use ($course) {
        return $courses->contains($course);
      },
    ]);
});

test('multiStore method validates and stores multiple data', function () {
  $course = Course::factory()
    ->institution($this->institution)
    ->create();
  $courseSessions = CourseSession::factory(2)->course($course)->create();

  $data = [
    'subjects' => [
      [
        'course_id' => $course->id,
        'course_session_id' => $courseSessions[0]->id,
        'num_of_questions' => 10,
      ],
      [
        'course_id' => $course->id,
        'course_session_id' => $courseSessions[1]->id,
        'num_of_questions' => 15,
      ],
    ],
  ];

  $response = $this->post(
    route('institutions.event-courses.multi-store', [
      $this->institution,
      $this->event,
    ]),
    $data,
  );

  $response
    ->assertRedirect(
      route('institutions.event-courses.index', [
        $this->institution,
        $this->event,
      ]),
    )
    ->assertSessionHas('message', 'Multiple event courses added');

  foreach ($data['subjects'] as $subject) {
    $this->assertDatabaseHas('event_courses', [
      'course_session_id' => $subject['course_session_id'],
      'num_of_questions' => $subject['num_of_questions'],
    ]);
  }
});

test('destroy method deletes the event course', function () {
  $eventCourse = EventCourse::factory()->create();

  $response = $this->get(
    route('institutions.event-courses.destroy', [
      $this->institution,
      $eventCourse,
    ]),
  );

  $response
    ->assertRedirect(
      route('institutions.event-courses.index', [
        $this->institution,
        $eventCourse->event_id,
      ]),
    )
    ->assertSessionHas('message', 'Event course deleted');

  $this->assertDatabaseMissing('event_courses', [
    'id' => $eventCourse->id,
  ]);
});
