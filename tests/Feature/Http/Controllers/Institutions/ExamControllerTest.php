<?php

use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Event;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Institution;
use App\Models\Student;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;

beforeEach(function () {
    $this->institution = Institution::factory()->user()->create();
    $this->assignedUser = $this->institution->institutionUsers()->first()->user;
    actingAs($this->assignedUser);
});

it('displays a list of exams for an event', function () {
    $event = Event::factory()->create();
    Exam::factory()->count(3)->for($event)->create();

    $response = $this->get(
        route('institutions.exams.index', [$this->institution, $event]),
    );

    $response->assertOk();
    $response->assertViewIs('institutions.exams.index');
    $response->assertViewHas('allRecords');
    $response->assertViewHas('event', $event);
});

it('displays the create exam view with relevant data', function () {
    $student = Student::factory()->create();
    $events = Event::factory()->count(2)->active()->create();

    $response = $this->get(
        route('institutions.exams.create', [$this->institution, $student]),
    );

    $response->assertOk();
    $response->assertViewIs('institutions.exams.create');
    $response->assertViewHasAll(['events', 'students', 'student', 'event']);
});

it('stores a new exam successfully', function () {
    $this->institution = Institution::factory()->create();
    $event = Event::factory()->create();
    $student = Student::factory()->create();
    $courseSessions = CourseSession::factory(2)->create();
    $eventCourses = $event
        ->eventCourses()
        ->createMany([
            ['course_session_id' => $courseSessions[0]->id],
            ['course_session_id' => $courseSessions[1]->id],
        ]);

    $data = [
        'event_id' => $event->id,
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
    $event = Event::factory()->create();

    $response = $this->get(
        route('institutions.exams.events.grades.create', [
            $this->institution,
            $event,
        ]),
    );

    $response->assertOk();
    $response->assertViewIs('institutions.exams.create-grade-exam');
    $response->assertViewHasAll(['event', 'eventCourses', 'grades']);
});

it('stores grade exams for multiple students', function () {
    $event = Event::factory()->create();
    $grade = Grade::factory()->create();
    Student::factory()->count(3)->for($grade)->create();
    [$courseSession1, $courseSession2] = CourseSession::factory(2)->create();
    $eventCourses = $event
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
            $event,
        ]),
        $data,
    )->assertRedirect();
});

it('stores multiple exams for different students', function () {
    $event = Event::factory()->create();
    $students = Student::factory()->count(2)->create();
    [$courseSession1, $courseSession2] = CourseSession::factory(2)->create();
    $eventCourses = $event
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
            $event,
        ]),
        $data,
    )->assertRedirect();
});

it('deletes an exam successfully', function () {
    $event = Event::factory()->create();
    $exam = Exam::factory()->for($event)->create();

    $response = $this->get(
        route('institutions.exams.destroy', [$this->institution, $exam]),
    );

    $response->assertRedirect();
    $response->assertSessionHas('message', 'Exam deleted');
});
