<?php

use App\Actions\PullEventCourseContent;
use App\Enums\ExamStatus;
use App\Helpers\ExamHandler;
use App\Http\Requests\UploadSessionQuestionsRequest;
use App\Models\Event;
use App\Models\EventCourse;
use App\Models\Exam;
use App\Models\ExamActivation;
use App\Models\ExamCourse;
use App\Models\Institution;
use App\Support\ExamProcess;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
  config(['exam.restrict_unactivated_exam_access_before_start' => false]);
  Http::preventStrayRequests();
  $this->institution = Institution::factory()->user()->create();
  $this->event = Event::factory()
    ->institution($this->institution)
    ->create();
  $this->eventCourse = EventCourse::factory()
    ->event($this->event, 2)
    ->create();
  $this->exam = Exam::factory()
    ->event($this->event)
    ->create([
      'status' => ExamStatus::Pending,
      'start_time' => null,
      'end_time' => null,
    ]);
  $this->examCourse = ExamCourse::factory()
    ->for($this->exam)
    ->create([
      'course_session_id' => $this->eventCourse->course_session_id,
    ]);
  $this->session = $this->eventCourse->courseSession
    ->load('course', 'questions', 'instructions', 'passages')
    ->toArray();
  $this->external = function () {
    // Only the external marker is needed; avoid persisting a foreign key.
    Event::retrieved(function ($event) {
      $event->external_content_id = 1;
      $event->external_event_courses = [
        ['course_session_id' => $this->session['id']],
      ];
    });
  };
});

afterEach(function () {
  Event::flushEventListeners();
});

it('redirects guests from the dashboard to login', function () {
  $this->get('/dashboard')->assertRedirect(route('login'));
});

it('renders event details including subjects', function () {
  $this->actingAs($this->institution->institutionUsers->first()->user)
    ->get(route('institutions.events.show', [$this->institution, $this->event]))
    ->assertOk()
    ->assertSee($this->event->title)
    ->assertSee($this->session['course']['course_code']);
});

it('renders an activated result without a start date', function () {
  $activation = ExamActivation::create([
    'institution_id' => $this->institution->id,
    'event_id' => $this->event->id,
    'num_of_exams' => 1,
    'licenses' => 1,
    'license_balance_before' => 1,
    'license_balance_after' => 0,
  ]);
  $this->exam->update([
    'status' => ExamStatus::Ended,
    'exam_activation_id' => $activation->id,
  ]);
  $this->get(route('exams.view-result', $this->exam->exam_no))
    ->assertOk()
    ->assertSee('Not recorded');
});

it('rejects missing local content before starting the exam', function () {
  $this->eventCourse->delete();
  $this->postJson(route('api.exam-start'), ['exam_no' => $this->exam->exam_no])
    ->assertUnprocessable()
    ->assertJsonValidationErrors('content');
  expect($this->exam->fresh()->status)->toBe(ExamStatus::Pending);
  expect($this->exam->fresh()->start_time)->toBeNull();
});

it(
  'does not partially score an exam when another course is missing',
  function () {
    $this->exam->update(['status' => ExamStatus::Active]);
    $missing = EventCourse::factory()
      ->event($this->event, 2)
      ->create();
    ExamCourse::factory()
      ->for($this->exam)
      ->create([
        'course_session_id' => $missing->course_session_id,
      ]);
    $missing->delete();
    $before = $this->examCourse->fresh()->getAttributes();
    $this->postJson(route('api.end-exam', $this->exam->exam_no))
      ->assertUnprocessable()
      ->assertJsonValidationErrors('content');
    expect($this->exam->fresh()->status)->toBe(ExamStatus::Active);
    expect($this->examCourse->fresh()->getAttributes())->toBe($before);
  },
);

it('rejects bad upstream content without starting the exam', function (
  $body,
  $status,
) {
  ($this->external)();
  Http::fake(['*' => Http::response($body, $status)]);
  $this->postJson(route('api.exam-start'), ['exam_no' => $this->exam->exam_no])
    ->assertUnprocessable()
    ->assertJsonValidationErrors('content');
  expect($this->exam->fresh()->status)->toBe(ExamStatus::Pending);
})->with([
  '405 HTML' => ['<html>Method Not Allowed</html>', 405],
  'invalid JSON' => ['not JSON', 200],
  'missing envelope' => [[], 200],
  'empty sessions' => [['course_sessions' => []], 200],
  'false session' => [['course_sessions' => [false]], 200],
]);

it('handles connection failure as a content validation error', function () {
  ($this->external)();
  Http::fake(
    fn() => throw new \Illuminate\Http\Client\ConnectionException('Timeout'),
  );
  $this->postJson(route('api.exam-start'), ['exam_no' => $this->exam->exam_no])
    ->assertUnprocessable()
    ->assertJsonValidationErrors('content');
  expect($this->exam->fresh()->status)->toBe(ExamStatus::Pending);
});

it(
  'rejects an upstream response that omits the requested session',
  function () {
    ($this->external)();
    $session = $this->session;
    $session['id'] += 10000;
    Http::fake(['*' => Http::response(['course_sessions' => [$session]])]);
    $this->postJson(route('api.exam-start'), [
      'exam_no' => $this->exam->exam_no,
    ])
      ->assertUnprocessable()
      ->assertJsonValidationErrors('content');
  },
);

it(
  'loads external questions over HTTPS and removes answers from startup data',
  function () {
    ($this->external)();
    Http::fake([
      '*' => Http::response(['course_sessions' => [$this->session]]),
    ]);
    $handler = Mockery::mock(ExamHandler::class);
    $handler
      ->shouldReceive('syncExamFile')
      ->once()
      ->andReturn(ExamProcess::success());
    $handler
      ->shouldReceive('getContent')
      ->once()
      ->andReturn(ExamProcess::success()->examTrack(['attempts' => []]));
    $this->app->instance(ExamHandler::class, $handler);
    $response = $this->postJson(route('api.exam-start'), [
      'exam_no' => $this->exam->exam_no,
    ]);
    $response
      ->assertOk()
      ->assertJsonCount(2, 'data.exam.exam_courses.0.course_session.questions')
      ->assertJsonPath(
        'data.exam.exam_courses.0.course_session.questions.0.answer',
        null,
      )
      ->assertJsonPath(
        'data.exam.exam_courses.0.course_session.questions.0.answer_meta',
        null,
      )
      ->assertJsonPath('data.exam.event.external_event_courses', []);
    expect($this->exam->fresh()->status)->toBe(ExamStatus::Active);
    Http::assertSent(
      fn($request) => $request->method() === 'POST' &&
        $request->url() ===
          'https://content.examscholars.com/api/course-sessions/retrieve' &&
        $request['subjects'] === [
          ['course_session_id' => $this->session['id']],
        ],
    );
  },
);

it('returns failure when the exam tracking file cannot be read', function () {
  $handler = Mockery::mock(ExamHandler::class);
  $handler->shouldReceive('syncExamFile')->andReturn(ExamProcess::success());
  $handler
    ->shouldReceive('getContent')
    ->andReturn(ExamProcess::fail('Exam file not found'));
  $this->app->instance(ExamHandler::class, $handler);
  $this->postJson(route('api.exam-start'), ['exam_no' => $this->exam->exam_no])
    ->assertUnauthorized()
    ->assertJsonPath('success', false);
});

it(
  'does not report success or change scores when tracking data is unavailable',
  function () {
    $this->exam->update(['status' => ExamStatus::Active]);
    $handler = Mockery::mock(ExamHandler::class);
    $handler
      ->shouldReceive('calculateScoreFromFile')
      ->andReturn(ExamProcess::fail('Exam file not found'));
    $this->app->instance(ExamHandler::class, $handler);
    $this->postJson(route('api.end-exam', $this->exam->exam_no))
      ->assertUnauthorized()
      ->assertJsonPath('success', false);
    expect($this->exam->fresh()->status)->toBe(ExamStatus::Active);
  },
);

it(
  'returns file validation errors for missing or unreadable spreadsheet uploads',
  function ($kind) {
    $files =
      $kind === 'missing'
        ? []
        : [
          'file' => UploadedFile::fake()->createWithContent(
            'questions.xlsx',
            "\x00\x01invalid spreadsheet",
          ),
        ];
    $request = UploadSessionQuestionsRequest::create(
      '/upload',
      'POST',
      [],
      [],
      $files,
    );
    $request->setContainer($this->app)->setRedirector($this->app['redirect']);
    try {
      $request->validateResolved();
      $this->fail('Expected a file validation error');
    } catch (ValidationException $exception) {
      expect($exception->errors())->toHaveKey('file');
    }
  },
)->with(['missing', 'corrupt']);

it('reads a valid spreadsheet upload', function () {
  $file = UploadedFile::fake()->createWithContent(
    'questions.csv',
    "Number,Question,A,B,C,D,E,Answer\n1,Two plus two?,4,5,6,7,8,A\n",
  );
  $rows = (new \App\Actions\Sheet\ConvertSheetToArray($file, [
    'A' => 'number',
    'B' => 'question',
  ]))->run();
  expect($rows)->toBe([['number' => '1', 'question' => 'Two plus two?']]);
});

it('scores external questions from a real tracking file', function () {
  ($this->external)();
  Http::fake(['*' => Http::response(['course_sessions' => [$this->session]])]);
  $this->exam->update(['status' => ExamStatus::Active]);
  $file = tempnam(sys_get_temp_dir(), 'exam-regression-');
  $question = $this->session['questions'][0];
  file_put_contents(
    $file,
    json_encode([
      'exam' => ['status' => 'active', 'end_time' => now()->subMinute()->toDateTimeString()],
      'attempts' => [$question['id'] => $question['answer']],
    ]),
  );
  $handler = Mockery::mock(ExamHandler::class)->makePartial();
  $handler->shouldReceive('getFullFilepath')->andReturn($file);
  $this->app->instance(ExamHandler::class, $handler);
  try {
    $this->postJson(route('api.end-exam', $this->exam->exam_no))
      ->assertOk()
      ->assertJsonPath('success', true);
    expect($this->exam->fresh()->status)->toBe(ExamStatus::Ended);
    expect($this->exam->fresh()->score)->toEqual(1);
    expect($this->examCourse->fresh()->num_of_questions)->toEqual(2);
    expect(json_decode(file_get_contents($file), true)['exam']['status'])->toBe(
      'ended',
    );
  } finally {
    unlink($file);
  }
});
