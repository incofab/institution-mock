<?php

namespace App\Console\Commands;

use App\Actions\ActivateExam;
use App\Actions\CreditLicenseFunding;
use App\Models\Exam;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Console\Command;

class ActivateExistingExams extends Command
{
  protected $signature = 'exams:activate-existing {institutionCode? : Optional institution code}';

  protected $description = 'Fund and activate all existing unactivated exams by institution';

  function handle(
    ActivateExam $activateExam,
    CreditLicenseFunding $creditLicenseFunding,
  ): int {
    $institutionCode = $this->argument('institutionCode');

    $institutions = Institution::query()
      ->when(
        $institutionCode,
        fn($query) => $query->where('code', $institutionCode),
      )
      ->orderBy('id')
      ->get();

    if ($institutionCode && $institutions->isEmpty()) {
      $this->error("Institution with code {$institutionCode} was not found.");
      return self::FAILURE;
    }

    if ($institutions->isEmpty()) {
      $this->info('No institutions found.');
      return self::SUCCESS;
    }

    $totalFundedLicenses = 0;
    $totalActivatedExams = 0;

    foreach ($institutions as $institution) {
      $unactivatedExamCount = $this->unactivatedExamQuery($institution)->count();

      if ($unactivatedExamCount < 1) {
        $this->line("{$institution->code} - {$institution->name}: no unactivated exams.");
        continue;
      }

      $fundingUser = $this->fundingUser($institution);
      if (!$fundingUser) {
        $this->error("{$institution->code} - {$institution->name}: no user found for funding record.");
        continue;
      }

      $licenseCost = (float) $institution->license_cost;
      if ($licenseCost <= 0) {
        $this->error("{$institution->code} - {$institution->name}: license cost must be greater than zero.");
        continue;
      }

      $amount = round($unactivatedExamCount * $licenseCost, 2);
      $reference =
        'EXISTING-EXAMS-' .
        $institution->code .
        '-' .
        now()->format('YmdHis');

      $creditLicenseFunding->run(
        $institution,
        $fundingUser,
        $amount,
        $licenseCost,
        $unactivatedExamCount,
        0,
        'existing-exams',
        $reference,
      );

      $activatedForInstitution = 0;
      $eventIds = $this->unactivatedExamQuery($institution)
        ->select('event_id')
        ->distinct()
        ->pluck('event_id');

      foreach ($institution->events()->whereIn('id', $eventIds)->get() as $event) {
        $activatedForInstitution += $activateExam->event(
          $institution->fresh(),
          $event,
        );
      }

      $totalFundedLicenses += $unactivatedExamCount;
      $totalActivatedExams += $activatedForInstitution;

      $this->info(
        "{$institution->code} - {$institution->name}: funded {$unactivatedExamCount} license(s), activated {$activatedForInstitution} exam(s).",
      );
    }

    $this->info(
      "Done. Funded {$totalFundedLicenses} license(s), activated {$totalActivatedExams} exam(s).",
    );

    return self::SUCCESS;
  }

  private function unactivatedExamQuery(Institution $institution)
  {
    return Exam::query()
      ->where('institution_id', $institution->id)
      ->whereNull('exam_activation_id');
  }

  private function fundingUser(Institution $institution): ?User
  {
    $institutionUser = $institution
      ->institutionUsers()
      ->with('user')
      ->first();

    return User::query()
      ->where('email', config('app.admin.email'))
      ->first() ??
      User::query()->find($institution->created_by_user_id) ??
      $institutionUser?->user ??
      User::query()->first();
  }
}
