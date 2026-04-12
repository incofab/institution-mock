<?php

use App\Actions\EndExam;
use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Models\Course;
use App\Models\CourseSession;
use App\Models\Event;
use App\Models\EventCourse;
use App\Models\Exam;
use App\Models\ExamActivation;
use App\Models\ExamCourse;
use App\Models\Institution;
use App\Models\Student;
use Illuminate\Testing\Fluent\AssertableJson;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

beforeEach(function () {
  $this->institution = Institution::factory()->create();
  $this->event = Event::factory()
    ->institution($this->institution)
    ->create();
  $this->student = Student::factory()
    ->institution($this->institution)
    ->create();
  $this->exam = Exam::factory()
    ->event($this->event)
    ->for($this->student)
    ->create();

  $this->eventCourse = EventCourse::factory()
    ->event($this->event, 2)
    ->create();
  $this->examCourse = ExamCourse::factory()
    ->for($this->exam)
    ->create(['course_session_id' => $this->eventCourse->course_session_id]);

  $this->activateExam = function (Exam $exam) {
    $activation = ExamActivation::query()->create([
      'institution_id' => $this->institution->id,
      'event_id' => $this->event->id,
      'num_of_exams' => 1,
      'licenses' => 1,
      'license_balance_before' => 1,
      'license_balance_after' => 0,
    ]);

    $exam->update(['exam_activation_id' => $activation->id]);
    return $activation;
  };
});

test('index returns a list of exams', function () {
  ($this->activateExam)($this->exam);

  $course = Course::factory()
    ->for($this->institution)
    ->create();
  $courseSession = CourseSession::factory()->for($course)->create();
  ExamCourse::factory()
    ->for($this->exam)
    ->for($courseSession)
    ->create();

  getJson(
    route('api.institutions.events.exams.index', [
      $this->institution,
      $this->event,
    ]),
  )
    ->assertOk()
    ->assertJsonStructure([
      'data' => [
        '*' => [
          'id',
          'exam_no',
          'exam_courses' => [
            '*' => ['id', 'exam_id', 'course_session_id', 'course_session'],
          ],
        ],
      ],
    ]);
});

test('index blocks offline event exam download until all event exams are activated', function () {
  getJson(
    route('api.institutions.events.exams.index', [
      $this->institution,
      $this->event,
    ]),
  )
    ->assertUnauthorized()
    ->assertJson(
      fn(AssertableJson $json) => $json
        ->where('success', false)
        ->where(
          'message',
          'Exams in this event are not available until all exams have been activated.',
        )
        ->etc(),
    );
});

test('startExam starts an exam and returns exam data', function () {
  ($this->activateExam)($this->exam);

  // Mock ExamHandler to avoid file system interaction
  $mockExamHandler = Mockery::mock(ExamHandler::class);
  $mockExamHandler->shouldReceive('syncExamFile')->andReturn(successRes());
  $mockExamHandler
    ->shouldReceive('getContent')
    ->andReturn(successRes('', ['exam_track' => ['question1', 'question2']]));
  $this->app->instance(ExamHandler::class, $mockExamHandler);

  postJson(route('api.exam-start'), [
    'exam_no' => 'INVALID_EXAM_NO',
  ])->assertJsonValidationErrorFor('exam_no');

  postJson(route('api.exam-start'), [
    'exam_no' => $this->exam->exam_no,
  ])
    ->assertOk()
    ->assertJsonStructure([
      'data' => ['exam_track', 'timeRemaining', 'baseUrl', 'exam'],
    ])
    ->assertJson(
      fn(AssertableJson $json) => $json
        ->has(
          'data.exam',
          fn(AssertableJson $json) => $json
            ->where('id', $this->exam->id)
            ->etc(),
        )
        ->etc(),
    );

  $this->assertDatabaseHas('exams', [
    'id' => $this->exam->id,
    'status' => ExamStatus::Active->value,
  ]);
});

test('startExam blocks unactivated exams when configured to restrict before start', function () {
  postJson(route('api.exam-start'), [
    'exam_no' => $this->exam->exam_no,
  ])
    ->assertUnauthorized()
    ->assertJson(
      fn(AssertableJson $json) => $json
        ->where('success', false)
        ->where('message', 'This exam has not been activated yet.')
        ->etc(),
    );
});

test('startExam allows unactivated exams when before-start restriction is disabled', function () {
  config(['exam.restrict_unactivated_exam_access_before_start' => false]);

  $mockExamHandler = Mockery::mock(ExamHandler::class);
  $mockExamHandler->shouldReceive('syncExamFile')->andReturn(successRes());
  $mockExamHandler
    ->shouldReceive('getContent')
    ->andReturn(successRes('', ['exam_track' => ['question1', 'question2']]));
  $this->app->instance(ExamHandler::class, $mockExamHandler);

  postJson(route('api.exam-start'), [
    'exam_no' => $this->exam->exam_no,
  ])
    ->assertOk()
    ->assertJsonStructure([
      'data' => ['exam_track', 'timeRemaining', 'baseUrl', 'exam'],
    ]);
});

test('uploadEventResult updates exam and exam course records', function () {
  $examCourseData = [
    'course_session_id' => $this->eventCourse->course_session_id,
    'score' => 9,
    'course_code' => 'CS101',
    'session' => '2023/2024',
    'status' => 'active',
    'num_of_questions' => 10,
  ];
  $examData = [
    'id' => $this->exam->id,
    'attempts' => [123 => 'A', 456 => 'B'],
    'time_remaining' => 120,
    'start_time' => now()->toDateTimeString(),
    'pause_time' => null,
    'end_time' => now()->addMinutes(2)->toDateTimeString(),
    'score' => 9,
    'status' => ExamStatus::Ended->value,
    'num_of_questions' => 10,
  ];
  $postData = [
    'exams' => [
      [
        'exam_courses' => [$examCourseData],
        ...$examData,
      ],
    ],
  ];
  $response = $this->postJson(
    route('api.institutions.exams.upload', [$this->institution]),
    $postData,
  );

  $response
    ->assertStatus(200)
    ->assertJson(
      fn(AssertableJson $json) => $json
        ->where('message', 'Exam records updated')
        ->etc(),
    );

  $this->assertDatabaseHas(
    'exams',
    collect($examData)->only('id', 'score', 'num_of_questions')->toArray(),
  );

  $this->assertDatabaseHas('exam_courses', [
    'exam_id' => $this->exam->id,
    ...collect($examCourseData)
      ->only('course_session_id', 'score', 'num_of_questions', 'session')
      ->toArray(),
  ]);
});

test('endExam ends an exam', function () {
  postJson(route('api.end-exam', [$this->exam->exam_no]))
    ->assertStatus(200)
    ->assertJson(
      fn(AssertableJson $json) => $json
        ->where('message', 'Exam ended successfully')
        ->etc(),
    );
});
