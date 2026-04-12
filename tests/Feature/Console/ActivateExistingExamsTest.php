<?php

use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamActivation;
use App\Models\Institution;
use App\Models\User;

it('funds and activates existing exams for the requested institution', function () {
  User::factory()->admin()->create();
  $institution = Institution::factory()->create([
    'licenses' => 2,
    'license_cost' => 250,
  ]);
  $otherInstitution = Institution::factory()->create([
    'licenses' => 0,
    'license_cost' => 300,
  ]);
  $firstEvent = Event::factory()
    ->institution($institution)
    ->create();
  $secondEvent = Event::factory()
    ->institution($institution)
    ->create();
  $otherEvent = Event::factory()
    ->institution($otherInstitution)
    ->create();

  Exam::factory()
    ->count(2)
    ->event($firstEvent)
    ->create();
  Exam::factory()
    ->event($secondEvent)
    ->create();
  Exam::factory()
    ->count(2)
    ->event($otherEvent)
    ->create();

  $this->artisan('exams:activate-existing', [
    'institutionCode' => $institution->code,
  ])
    ->expectsOutputToContain("{$institution->code} - {$institution->name}: funded 3 license(s), activated 3 exam(s).")
    ->assertExitCode(0);

  expect(
    Exam::query()
      ->where('institution_id', $institution->id)
      ->whereNull('exam_activation_id')
      ->count(),
  )->toBe(0)
    ->and(
      Exam::query()
        ->where('institution_id', $otherInstitution->id)
        ->whereNull('exam_activation_id')
        ->count(),
    )
    ->toBe(2);

  $this->assertDatabaseHas('fundings', [
    'institution_id' => $institution->id,
    'amount' => 750,
    'license_cost' => 250,
    'num_of_licenses' => 3,
    'source' => 'existing-exams',
  ]);
  $this->assertDatabaseHas('institutions', [
    'id' => $institution->id,
    'licenses' => 2,
  ]);
  expect(
    ExamActivation::query()
      ->where('institution_id', $institution->id)
      ->count(),
  )->toBe(2);
});

it('funds and activates existing exams for all institutions', function () {
  User::factory()->admin()->create();
  $firstInstitution = Institution::factory()->create([
    'licenses' => 0,
    'license_cost' => 200,
  ]);
  $secondInstitution = Institution::factory()->create([
    'licenses' => 1,
    'license_cost' => 300,
  ]);
  $firstEvent = Event::factory()
    ->institution($firstInstitution)
    ->create();
  $secondEvent = Event::factory()
    ->institution($secondInstitution)
    ->create();

  Exam::factory()
    ->count(2)
    ->event($firstEvent)
    ->create();
  Exam::factory()
    ->count(3)
    ->event($secondEvent)
    ->create();

  $this->artisan('exams:activate-existing')
    ->expectsOutputToContain('Done. Funded 5 license(s), activated 5 exam(s).')
    ->assertExitCode(0);

  expect(
    Exam::query()
      ->whereNull('exam_activation_id')
      ->count(),
  )->toBe(0);

  $this->assertDatabaseHas('fundings', [
    'institution_id' => $firstInstitution->id,
    'amount' => 400,
    'license_cost' => 200,
    'num_of_licenses' => 2,
    'source' => 'existing-exams',
  ]);
  $this->assertDatabaseHas('fundings', [
    'institution_id' => $secondInstitution->id,
    'amount' => 900,
    'license_cost' => 300,
    'num_of_licenses' => 3,
    'source' => 'existing-exams',
  ]);
  $this->assertDatabaseHas('institutions', [
    'id' => $firstInstitution->id,
    'licenses' => 0,
  ]);
  $this->assertDatabaseHas('institutions', [
    'id' => $secondInstitution->id,
    'licenses' => 1,
  ]);
});

it('returns failure when the institution code does not exist', function () {
  $this->artisan('exams:activate-existing', [
    'institutionCode' => 'missing-code',
  ])
    ->expectsOutput('Institution with code missing-code was not found.')
    ->assertExitCode(1);
});
