<?php

namespace App\Services\Payments\Gateways;

use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentInitData;
use App\Services\Payments\PaymentInitResult;
use App\Services\Payments\PaymentVerifyResult;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FlutterwaveGateway implements PaymentGateway
{
  function key(): string
  {
    return 'flutterwave';
  }

  function label(): string
  {
    return 'Flutterwave';
  }

  function initialize(PaymentInitData $data): PaymentInitResult
  {
    if (!config('services.flutterwave.secret_key')) {
      return PaymentInitResult::failed('Flutterwave secret key is not configured');
    }

    $response = Http::withToken(config('services.flutterwave.secret_key'))
      ->acceptJson()
      ->post('https://api.flutterwave.com/v3/payments', [
        'tx_ref' => $data->reference,
        'amount' => $data->amount,
        'currency' => 'NGN',
        'redirect_url' => $data->callbackUrl,
        'customer' => [
          'email' => $data->email,
          'name' => $data->customerName,
        ],
        'customizations' => [
          'title' => config('app.name'),
          'description' => $data->description,
        ],
      ]);

    $payload = $response->json() ?? [];
    $redirectUrl = Arr::get($payload, 'data.link');

    if (!$response->ok() || Arr::get($payload, 'status') !== 'success' || !$redirectUrl) {
      return PaymentInitResult::failed(
        Arr::get($payload, 'message', 'Flutterwave initialization failed'),
        $payload,
      );
    }

    return PaymentInitResult::success($redirectUrl, $data->reference, $payload);
  }

  function verify(string $reference): PaymentVerifyResult
  {
    if (!config('services.flutterwave.secret_key')) {
      return PaymentVerifyResult::failed('Flutterwave secret key is not configured');
    }

    $response = Http::withToken(config('services.flutterwave.secret_key'))
      ->acceptJson()
      ->get('https://api.flutterwave.com/v3/transactions/verify_by_reference', [
        'tx_ref' => $reference,
      ]);

    $payload = $response->json() ?? [];
    $status = Arr::get($payload, 'data.status');

    if (!$response->ok() || Arr::get($payload, 'status') !== 'success' || $status !== 'successful') {
      return PaymentVerifyResult::failed(
        Arr::get($payload, 'message', 'Flutterwave transaction not successful'),
        $status,
        $payload,
      );
    }

    return PaymentVerifyResult::success(
      (float) Arr::get($payload, 'data.amount'),
      $status,
      $payload,
    );
  }
}
