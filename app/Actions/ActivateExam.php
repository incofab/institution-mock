<?php

namespace App\Actions;

use App\Models\Event;
use App\Models\Exam;
use App\Models\ExamActivation;
use App\Models\Institution;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ActivateExam
{
  const LICENSE_COST = 1;

  function exam(Institution $institution, Exam $exam): int
  {
    abort_if($exam->institution_id !== $institution->id, 404);

    return DB::transaction(function () use ($institution, $exam) {
      $institution = $this->lockInstitution($institution);
      $exam = Exam::query()
        ->with('activation')
        ->lockForUpdate()
        ->findOrFail($exam->id);

      if ($exam->activation) {
        return 0;
      }

      $this->ensureEnoughLicenses($institution, self::LICENSE_COST);
      $activation = $this->createActivation(
        $institution,
        $exam->event_id,
        1,
      );

      $exam->forceFill(['exam_activation_id' => $activation->id])->save();

      return 1;
    });
  }

  function event(Institution $institution, Event $event): int
  {
    abort_if($event->institution_id !== $institution->id, 404);

    return DB::transaction(function () use ($institution, $event) {
      $institution = $this->lockInstitution($institution);
      $exams = $event
        ->exams()
        ->whereNull('exam_activation_id')
        ->lockForUpdate()
        ->get();

      if ($exams->isEmpty()) {
        return 0;
      }

      $licenseCost = $exams->count() * self::LICENSE_COST;
      $this->ensureEnoughLicenses($institution, $licenseCost);

      $activation = $this->createActivation(
        $institution,
        $event->id,
        $exams->count(),
      );

      Exam::query()
        ->whereIn('id', $exams->pluck('id'))
        ->update(['exam_activation_id' => $activation->id]);

      return $exams->count();
    });
  }

  private function lockInstitution(Institution $institution): Institution
  {
    return Institution::query()
      ->lockForUpdate()
      ->findOrFail($institution->id);
  }

  private function ensureEnoughLicenses(
    Institution $institution,
    int $licenseCost,
  )
  {
    if ($institution->licenses >= $licenseCost) {
      return;
    }

    throw ValidationException::withMessages([
      'licenses' => "Insufficient license balance. You need {$licenseCost} license(s), but only have {$institution->licenses}.",
    ]);
  }

  private function createActivation(
    Institution $institution,
    int $eventId,
    int $numOfExams,
  ): ExamActivation {
    $licenseBalanceBefore = $institution->licenses;
    $licenseCost = $numOfExams * self::LICENSE_COST;
    $licenseBalanceAfter = $licenseBalanceBefore - $licenseCost;

    $activation = ExamActivation::query()->create([
      'institution_id' => $institution->id,
      'event_id' => $eventId,
      'activated_by_user_id' => currentUser()?->id,
      'num_of_exams' => $numOfExams,
      'licenses' => $licenseCost,
      'license_balance_before' => $licenseBalanceBefore,
      'license_balance_after' => $licenseBalanceAfter,
    ]);

    $institution->forceFill(['licenses' => $licenseBalanceAfter])->save();

    return $activation;
  }
}
