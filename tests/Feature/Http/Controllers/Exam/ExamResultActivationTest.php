<?php

use App\Enums\ExamStatus;
use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamActivation;
use App\Models\Institution;
use App\Models\User;

use function Pest\Laravel\actingAs;

it('blocks result viewing until the exam is activated', function () {
  $institution = Institution::factory()->create();
  $event = Event::factory()
    ->institution($institution)
    ->create();
  $exam = Exam::factory()
    ->event($event)
    ->create([
      'status' => ExamStatus::Ended,
    ]);

  $this->get(route('exams.view-result', $exam->exam_no))
    ->assertRedirect(route('exams.view-result'))
    ->assertSessionHas('error', 'Result is not available yet');
});

it('blocks event result download when any exam is not activated', function () {
  $institution = Institution::factory()
    ->user()
    ->create();
  $user = $institution->institutionUsers()->first()->user;
  $event = Event::factory()
    ->institution($institution)
    ->create();
  Exam::factory()
    ->event($event)
    ->create(['status' => ExamStatus::Ended]);
  actingAs($user);

  $this->get(route('institutions.events.download', [$institution, $event]))
    ->assertRedirect()
    ->assertSessionHas(
      'error',
      'Results cannot be downloaded until all exams in this event have been activated.',
    );
});

it('allows event result download when all exams are activated', function () {
  User::factory()->admin()->create();
  $institution = Institution::factory()
    ->user()
    ->create();
  $user = $institution->institutionUsers()->first()->user;
  $event = Event::factory()
    ->institution($institution)
    ->create();
  $exam = Exam::factory()
    ->event($event)
    ->create(['status' => ExamStatus::Ended]);
  $activation = ExamActivation::query()->create([
    'institution_id' => $institution->id,
    'event_id' => $event->id,
    'activated_by_user_id' => $user->id,
    'num_of_exams' => 1,
    'licenses' => 1,
    'license_balance_before' => 1,
    'license_balance_after' => 0,
  ]);
  $exam->update(['exam_activation_id' => $activation->id]);
  actingAs($user);

  $this->get(route('institutions.events.download', [$institution, $event]))
    ->assertOk()
    ->assertHeader(
      'content-type',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    );
});
