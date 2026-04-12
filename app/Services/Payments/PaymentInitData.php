<?php

namespace App\Services\Payments;

class PaymentInitData
{
  function __construct(
    public float $amount,
    public string $email,
    public string $customerName,
    public string $reference,
    public string $callbackUrl,
    public string $description,
  ) {
  }
}
