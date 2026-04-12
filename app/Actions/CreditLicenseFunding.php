<?php

namespace App\Actions;

use App\Models\Funding;
use App\Models\GatewayPayment;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreditLicenseFunding
{
  function run(
    Institution $institution,
    User $user,
    float $amount,
    float $licenseCost,
    int $numOfLicenses,
    float $balanceAmount,
    string $source = 'manual',
    ?string $reference = null,
    ?Model $fundable = null,
    int $bonusLicenses = 0,
    ?string $comment = null,
  ): Funding {
    return DB::transaction(function () use (
      $institution,
      $user,
      $amount,
      $licenseCost,
      $numOfLicenses,
      $balanceAmount,
      $source,
      $reference,
      $fundable,
      $bonusLicenses,
      $comment,
    ) {
      if ($fundable) {
        $existingFunding = Funding::query()
          ->where('fundable_type', $fundable->getMorphClass())
          ->where('fundable_id', $fundable->id)
          ->first();

        if ($existingFunding) {
          return $existingFunding;
        }
      }

      $institution = Institution::query()
        ->lockForUpdate()
        ->findOrFail($institution->id);

      $licenseBalanceBefore = $institution->licenses;
      $licenseBalanceAfter = $licenseBalanceBefore + $numOfLicenses;

      $funding = $institution->fundings()->create([
        'user_id' => $user->id,
        'amount' => $amount,
        'license_cost' => $licenseCost,
        'num_of_licenses' => $numOfLicenses,
        'bonus_licenses' => $bonusLicenses,
        'balance_amount' => $balanceAmount,
        'license_balance_before' => $licenseBalanceBefore,
        'license_balance_after' => $licenseBalanceAfter,
        'source' => $source,
        'reference' => $reference,
        'comment' => $comment,
        'fundable_type' => $fundable?->getMorphClass(),
        'fundable_id' => $fundable?->id,
      ]);

      $institution->forceFill(['licenses' => $licenseBalanceAfter])->save();

      return $funding;
    });
  }

  function runForAmount(
    Institution $institution,
    User $user,
    float $amount,
    string $source = 'manual',
    ?string $reference = null,
    ?Model $fundable = null,
    int $bonusLicenses = 0,
    ?string $comment = null,
  ): Funding {
    return DB::transaction(function () use (
      $institution,
      $user,
      $amount,
      $source,
      $reference,
      $fundable,
      $bonusLicenses,
      $comment,
    ) {
      $institution = Institution::query()
        ->lockForUpdate()
        ->findOrFail($institution->id);

      $licenseCost = (float) $institution->license_cost;
      if ($licenseCost <= 0) {
        throw ValidationException::withMessages([
          'amount' => 'Institution license cost must be greater than zero.',
        ]);
      }

      $paidLicenses = (int) floor($amount / $licenseCost);
      $numOfLicenses = $paidLicenses + $bonusLicenses;
      if ($numOfLicenses < 1) {
        throw ValidationException::withMessages([
          'amount' => "Amount must be enough to fund at least one license at {$licenseCost}, or include bonus licenses.",
        ]);
      }

      return $this->run(
        $institution,
        $user,
        $amount,
        $licenseCost,
        $numOfLicenses,
        round($amount - $paidLicenses * $licenseCost, 2),
        $source,
        $reference,
        $fundable,
        $bonusLicenses,
        $comment,
      );
    });
  }

  function runFromGatewayPayment(GatewayPayment $gatewayPayment): Funding
  {
    return DB::transaction(function () use ($gatewayPayment) {
      $gatewayPayment = $gatewayPayment
        ->newQuery()
        ->lockForUpdate()
        ->findOrFail($gatewayPayment->id);

      return $this->runForAmount(
        $gatewayPayment->institution,
        $gatewayPayment->user,
        $gatewayPayment->amount,
        $gatewayPayment->gateway,
        $gatewayPayment->reference,
        $gatewayPayment,
      );
    });
  }
}
