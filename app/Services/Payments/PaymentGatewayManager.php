<?php

namespace App\Services\Payments;

use InvalidArgumentException;

class PaymentGatewayManager
{
  /** @var array<string, PaymentGateway> */
  private array $gateways;

  function __construct()
  {
    $this->gateways = collect(config('services.license_payments.gateways', []))
      ->mapWithKeys(function (string $gatewayClass) {
        /** @var PaymentGateway $gateway */
        $gateway = app($gatewayClass);
        return [$gateway->key() => $gateway];
      })
      ->all();
  }

  function get(string $key): PaymentGateway
  {
    if (!isset($this->gateways[$key])) {
      throw new InvalidArgumentException("Unsupported payment gateway: {$key}");
    }

    return $this->gateways[$key];
  }

  /** @return array<string, string> */
  function options(): array
  {
    return collect($this->gateways)
      ->mapWithKeys(fn(PaymentGateway $gateway) => [
        $gateway->key() => $gateway->label(),
      ])
      ->all();
  }
}
