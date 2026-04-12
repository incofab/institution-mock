<?php

namespace App\Services\Payments;

class PaymentVerifyResult
{
  function __construct(
    public bool $successful,
    public ?float $amount = null,
    public ?string $status = null,
    public ?string $message = null,
    public array $payload = [],
  ) {
  }

  static function success(
    float $amount,
    ?string $status = null,
    array $payload = [],
    ?string $message = null,
  ): self {
    return new self(true, $amount, $status, $message, $payload);
  }

  static function failed(
    string $message,
    ?string $status = null,
    array $payload = [],
  ): self {
    return new self(false, null, $status, $message, $payload);
  }
}
