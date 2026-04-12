<?php

namespace App\Services\Payments\Gateways;

use App\Services\Payments\PaymentGateway;
use App\Services\Payments\PaymentInitData;
use App\Services\Payments\PaymentInitResult;
use App\Services\Payments\PaymentVerifyResult;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class MonnifyGateway implements PaymentGateway
{
  function key(): string
  {
    return 'monnify';
  }

  function label(): string
  {
    return 'Monnify';
  }

  function initialize(PaymentInitData $data): PaymentInitResult
  {
    if (!config('services.monnify.contract_code')) {
      return PaymentInitResult::failed('Monnify contract code is not configured');
    }

    $token = $this->token();

    if (!$token) {
      return PaymentInitResult::failed('Monnify authentication failed');
    }

    $response = Http::withToken($token)
      ->acceptJson()
      ->post($this->baseUrl() . '/api/v1/merchant/transactions/init-transaction', [
        'amount' => $data->amount,
        'customerName' => $data->customerName,
        'customerEmail' => $data->email,
        'paymentReference' => $data->reference,
        'paymentDescription' => $data->description,
        'currencyCode' => 'NGN',
        'contractCode' => config('services.monnify.contract_code'),
        'redirectUrl' => $data->callbackUrl,
      ]);

    $payload = $response->json() ?? [];
    $redirectUrl = Arr::get($payload, 'responseBody.checkoutUrl');

    if (!$response->ok() || !Arr::get($payload, 'requestSuccessful') || !$redirectUrl) {
      return PaymentInitResult::failed(
        Arr::get($payload, 'responseMessage', 'Monnify initialization failed'),
        $payload,
      );
    }

    return PaymentInitResult::success(
      $redirectUrl,
      Arr::get($payload, 'responseBody.paymentReference', $data->reference),
      $payload,
    );
  }

  function verify(string $reference): PaymentVerifyResult
  {
    $token = $this->token();

    if (!$token) {
      return PaymentVerifyResult::failed('Monnify authentication failed');
    }

    $response = Http::withToken($token)
      ->acceptJson()
      ->get($this->baseUrl() . '/api/v2/transactions/' . urlencode($reference));

    $payload = $response->json() ?? [];
    $status = Arr::get($payload, 'responseBody.paymentStatus');

    if (!$response->ok() || !Arr::get($payload, 'requestSuccessful') || $status !== 'PAID') {
      return PaymentVerifyResult::failed(
        Arr::get($payload, 'responseMessage', 'Monnify transaction not successful'),
        $status,
        $payload,
      );
    }

    return PaymentVerifyResult::success(
      (float) Arr::get($payload, 'responseBody.amountPaid'),
      $status,
      $payload,
    );
  }

  private function token(): ?string
  {
    if (
      !config('services.monnify.api_key') ||
      !config('services.monnify.secret_key')
    ) {
      return null;
    }

    $response = Http::withBasicAuth(
      config('services.monnify.api_key'),
      config('services.monnify.secret_key'),
    )
      ->acceptJson()
      ->post($this->baseUrl() . '/api/v1/auth/login');

    if (!$response->ok() || !Arr::get($response->json() ?? [], 'requestSuccessful')) {
      return null;
    }

    return Arr::get($response->json(), 'responseBody.accessToken');
  }

  private function baseUrl(): string
  {
    return rtrim(config('services.monnify.base_url'), '/');
  }
}
