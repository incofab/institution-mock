<?php

namespace App\Services\Payments;

interface PaymentGateway
{
  function key(): string;

  function label(): string;

  function initialize(PaymentInitData $data): PaymentInitResult;

  function verify(string $reference): PaymentVerifyResult;
}
