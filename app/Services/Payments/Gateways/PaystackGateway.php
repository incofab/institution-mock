<?php

namespace App\Services\Payments\Gateways;

use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentInitData;
use App\Services\Payments\PaymentInitResult;
use App\Services\Payments\PaymentVerifyResult;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class PaystackGateway implements PaymentGateway
{
  function key(): string
  {
    return 'paystack';
  }

  function label(): string
  {
    return 'Paystack';
  }

  function initialize(PaymentInitData $data): PaymentInitResult
  {
    if (!config('services.paystack.secret_key')) {
      return PaymentInitResult::failed('Paystack secret key is not configured');
    }

    $response = Http::withToken(config('services.paystack.secret_key'))
      ->acceptJson()
      ->post('https://api.paystack.co/transaction/initialize', [
        'amount' => (int) round($data->amount * 100),
        'email' => $data->email,
        'callback_url' => $data->callbackUrl,
        'reference' => $data->reference,
      ]);

    $payload = $response->json() ?? [];
    $redirectUrl = Arr::get($payload, 'data.authorization_url');

    if (!$response->ok() || !Arr::get($payload, 'status') || !$redirectUrl) {
      return PaymentInitResult::failed(
        Arr::get($payload, 'message', 'Paystack initialization failed'),
        $payload,
      );
    }

    return PaymentInitResult::success(
      $redirectUrl,
      Arr::get($payload, 'data.reference', $data->reference),
      $payload,
    );
  }

  function verify(string $reference): PaymentVerifyResult
  {
    if (!config('services.paystack.secret_key')) {
      return PaymentVerifyResult::failed(
        'Paystack secret key is not configured',
      );
    }

    $response = Http::withToken(config('services.paystack.secret_key'))
      ->acceptJson()
      ->get("https://api.paystack.co/transaction/verify/{$reference}");

    $payload = $response->json() ?? [];
    $status = Arr::get($payload, 'data.status');

    if (
      !$response->ok() ||
      !Arr::get($payload, 'status') ||
      $status !== 'success'
    ) {
      return PaymentVerifyResult::failed(
        Arr::get(
          $payload,
          'data.gateway_response',
          'Paystack transaction not successful',
        ),
        $status,
        $payload,
      );
    }

    return PaymentVerifyResult::success(
      ((float) Arr::get($payload, 'data.amount')) / 100,
      $status,
      $payload,
    );
  }
}
