<?php
namespace App\Http\Controllers\Institutions;

use App\Actions\CreditLicenseFunding;
use App\Actions\BuildLicenseInvoice;
use App\Http\Controllers\Controller;
use App\Models\GatewayPayment;
use App\Models\Institution;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Payments\PaymentInitData;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InstitutionController extends Controller
{
  function index(Institution $institution)
  {
    $unactivatedExamsCount = $institution
      ->events()
      ->join('exams', 'events.id', '=', 'exams.event_id')
      ->whereNull('exams.exam_activation_id')
      ->count('exams.id');

    return view('institutions.index', [
      'students_count' => $institution->students()->count(),
      'events_count' => $institution->events()->count(),
      'grades_count' => $institution->grades()->count(),
      'courses_count' => $institution->courses()->count(),
      'licenses_count' => $institution->licenses,
      'unactivated_exams_count' => $unactivatedExamsCount,
      'pending_licenses_count' => max(
        $unactivatedExamsCount - $institution->licenses,
        0,
      ),
    ]);
  }

  function activationHistory(Institution $institution)
  {
    return view('institutions.activation-history', [
      'allRecords' => paginateFromRequest(
        $institution
          ->examActivations()
          ->with('event', 'activatedByUser')
          ->latest('id'),
      ),
    ]);
  }

  function fundingHistory(Institution $institution)
  {
    return view('institutions.funding-history', [
      'allRecords' => paginateFromRequest(
        $institution->fundings()->with('user')->latest('id'),
      ),
    ]);
  }

  function invoice(Institution $institution)
  {
    $invoice = (new BuildLicenseInvoice())->institution($institution);

    return response($invoice['content'], 200, [
      'Content-Type' => 'application/pdf',
      'Content-Disposition' => "attachment; filename=\"{$invoice['file_name']}\"",
    ]);
  }

  function fundLicensesView(
    Institution $institution,
    PaymentGatewayManager $gateways,
  ) {
    $unactivatedExamsCount = $institution
      ->events()
      ->join('exams', 'events.id', '=', 'exams.event_id')
      ->whereNull('exams.exam_activation_id')
      ->count('exams.id');

    return view('institutions.fund-licenses', [
      'gateways' => $gateways->options(),
      'unactivatedExamsCount' => $unactivatedExamsCount,
      'pendingLicensesCount' => max(
        $unactivatedExamsCount - $institution->licenses,
        0,
      ),
    ]);
  }

  function fundLicensesStore(
    Request $request,
    Institution $institution,
    PaymentGatewayManager $gateways,
  ) {
    $data = $request->validate([
      'amount' => ['required', 'numeric', 'min:0.01'],
      'gateway' => [
        'required',
        'string',
        'in:' . implode(',', array_keys($gateways->options())),
      ],
    ]);

    $licenseCost = (float) $institution->license_cost;
    if ($licenseCost <= 0) {
      throw ValidationException::withMessages([
        'amount' => 'Institution license cost must be greater than zero.',
      ]);
    }

    $amount = (float) $data['amount'];
    if ((int) floor($amount / $licenseCost) < 1) {
      throw ValidationException::withMessages([
        'amount' => "Amount must be enough to fund at least one license at {$licenseCost}.",
      ]);
    }

    $reference = 'LIC-' . $institution->id . '-' . Str::upper(Str::random(24));
    $gateway = $gateways->get($data['gateway']);

    $gatewayPayment = $institution->gatewayPayments()->create([
      'user_id' => currentUser()->id,
      'amount' => $amount,
      'gateway' => $gateway->key(),
      'reference' => $reference,
      'status' => GatewayPayment::STATUS_PENDING,
    ]);

    $init = $gateway->initialize(
      new PaymentInitData(
        amount: $amount,
        email: currentUser()->email,
        customerName: currentUser()->name,
        reference: $reference,
        callbackUrl: instRoute('fund-licenses.callback', [
          'gateway' => $gateway->key(),
          'reference' => $reference,
        ]),
        description: "License funding for {$institution->name}",
      ),
    );

    if (!$init->successful || !$init->redirectUrl) {
      $gatewayPayment
        ->forceFill([
          'status' => GatewayPayment::STATUS_FAILED,
          'gateway_payload' => $init->payload,
          'failed_at' => now(),
        ])
        ->save();

      return back()->with(
        'error',
        $init->message ?? 'Payment initialization failed',
      );
    }

    $gatewayPayment
      ->forceFill([
        'reference' => $init->reference ?? $reference,
        'status' => GatewayPayment::STATUS_INITIALIZED,
        'gateway_payload' => $init->payload,
        'initialized_at' => now(),
      ])
      ->save();

    return redirect()->away($init->redirectUrl);
  }

  function fundLicensesCallback(
    Request $request,
    Institution $institution,
    PaymentGatewayManager $gateways,
    string $gateway,
  ) {
    $reference =
      $request->query('reference') ??
      ($request->query('trxref') ??
        ($request->query('tx_ref') ?? $request->query('paymentReference')));

    if (!$reference) {
      return redirect(instRoute('fund-licenses.create'))->with(
        'error',
        'Payment reference was not supplied.',
      );
    }

    $gatewayPayment = $institution
      ->gatewayPayments()
      ->where('reference', $reference)
      ->where('gateway', $gateway)
      ->firstOrFail();

    $verify = $gateways->get($gateway)->verify($reference);
    if (!$verify->successful) {
      $gatewayPayment
        ->forceFill([
          'status' => GatewayPayment::STATUS_FAILED,
          'gateway_payload' => $verify->payload,
          'failed_at' => now(),
        ])
        ->save();

      return redirect(instRoute('fund-licenses.create'))->with(
        'error',
        $verify->message ?? 'Payment verification failed.',
      );
    }

    if (round($verify->amount, 2) < round($gatewayPayment->amount, 2)) {
      $gatewayPayment
        ->forceFill([
          'status' => GatewayPayment::STATUS_FAILED,
          'gateway_payload' => $verify->payload,
          'failed_at' => now(),
        ])
        ->save();

      return redirect(instRoute('fund-licenses.create'))->with(
        'error',
        'Payment amount is less than the requested funding amount.',
      );
    }

    (new CreditLicenseFunding())->runFromGatewayPayment($gatewayPayment);

    $gatewayPayment
      ->forceFill([
        'status' => GatewayPayment::STATUS_CREDITED,
        'gateway_payload' => $verify->payload,
        'verified_at' => now(),
      ])
      ->save();

    return redirect(instRoute('funding-history'))->with(
      'message',
      'Institution licenses funded successfully',
    );
  }
}
