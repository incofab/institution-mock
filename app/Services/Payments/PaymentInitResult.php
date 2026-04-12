<?php

namespace App\Services\Payments;

class PaymentInitResult
{
  function __construct(
    public bool $successful,
    public ?string $redirectUrl = null,
    public ?string $reference = null,
    public ?string $message = null,
    public array $payload = [],
  ) {
  }

  static function success(
    string $redirectUrl,
    string $reference,
    array $payload = [],
    ?string $message = null,
  ): self {
    return new self(true, $redirectUrl, $reference, $message, $payload);
  }

  static function failed(string $message, array $payload = []): self
  {
    return new self(false, null, null, $message, $payload);
  }
}
