<?php

use App\Helpers\ExamHandler;
use App\Models\Exam;
use App\Models\Institution;
use Illuminate\Support\Facades\File;

beforeEach(function () {
  $this->institution = Institution::factory()->create();
});

// Test suite for ExamHandler
it('creates or updates an exam file', function () {
  $exam = Exam::factory()
    ->institution($this->institution)
    ->make([
      'event_id' => 1,
      'exam_no' => '12345',
      'status' => 'active',
    ]);

  // Mock file operations
  File::shouldReceive('exists')
    ->once()
    ->with(EXAM_FILES_DIR . 'event_1/exam_12345.json')
    ->andReturn(false);

  File::shouldReceive('put')->once()->andReturn(true);

  $handler = new ExamHandler();
  $result = $handler->syncExamFile($exam);

  expect($result['success'])->toBeTrue();
  expect($result['message'])->toBe('Exam file ready');
});
return;

it('records student attempts', function () {
  $eventId = 1;
  $examNo = '12345';
  $studentAttempts = [
    'question_1' => 'A',
    'question_2' => 'B',
  ];

  $filePath = EXAM_FILES_DIR . "event_{$eventId}/exam_{$examNo}.json";

  $existingContent = [
    'exam' => ['id' => 1, 'status' => 'active'],
    'attempts' => [],
  ];

  File::shouldReceive('exists')->once()->with($filePath)->andReturn(true);

  File::shouldReceive('get')
    ->once()
    ->with($filePath)
    ->andReturn(json_encode($existingContent));

  File::shouldReceive('put')->once()->andReturn(true);

  $handler = new ExamHandler();
  $result = $handler->attemptQuestion($studentAttempts, $eventId, $examNo);

  expect($result['success'])->toBeTrue();
  expect($result['message'])->toBe('Attempt recorded');
});

it('ends an exam', function () {
  $eventId = 1;
  $examNo = '12345';

  $filePath = EXAM_FILES_DIR . "event_{$eventId}/exam_{$examNo}.json";

  $existingContent = [
    'exam' => ['id' => 1, 'status' => 'active'],
    'attempts' => [],
  ];

  File::shouldReceive('exists')->once()->with($filePath)->andReturn(true);

  File::shouldReceive('get')
    ->once()
    ->with($filePath)
    ->andReturn(json_encode($existingContent));

  File::shouldReceive('put')->once()->andReturn(true);

  $handler = new ExamHandler();
  $result = $handler->endExam($eventId, $examNo);

  expect($result['success'])->toBeTrue();
  expect($result['message'])->toBe('Exam ended');
});
