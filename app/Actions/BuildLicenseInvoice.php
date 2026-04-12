<?php

namespace App\Actions;

use App\Models\Event;
use App\Models\Institution;
use App\Support\SimplePdf;
use Illuminate\Support\Collection;

class BuildLicenseInvoice
{
  function event(Institution $institution, Event $event): array
  {
    abort_if($event->institution_id !== $institution->id, 404);

    $event->loadCount([
      'exams as unactivated_exams_count' => fn($query) => $query->whereNull(
        'exam_activation_id',
      ),
    ]);

    return $this->build(
      $institution,
      collect([$event]),
      "Event activation invoice - {$event->title}",
      "EVT-{$event->id}",
    );
  }

  function institution(Institution $institution): array
  {
    $events = $institution
      ->events()
      ->withCount([
        'exams as unactivated_exams_count' => fn($query) => $query->whereNull(
          'exam_activation_id',
        ),
      ])
      ->orderBy('title')
      ->get()
      ->filter(fn(Event $event) => $event->unactivated_exams_count > 0)
      ->values();

    return $this->build(
      $institution,
      $events,
      'Institution activation invoice',
      'ALL',
    );
  }

  private function build(
    Institution $institution,
    Collection $events,
    string $title,
    string $scopeCode,
  ): array {
    $licenseCost = (float) $institution->license_cost;
    $currentLicenses = (int) $institution->licenses;
    $requiredLicenses = (int) $events->sum('unactivated_exams_count');
    $licensesToBuy = max($requiredLicenses - $currentLicenses, 0);
    $amountDue = $licensesToBuy * $licenseCost;
    $invoiceNo =
      'INV-' .
      $institution->code .
      '-' .
      $scopeCode .
      '-' .
      now()->format('YmdHis');

    $pdf = new SimplePdf($title);
    foreach (
      $this->invoicePages(
        $institution,
        $events,
        $invoiceNo,
        $licenseCost,
        $currentLicenses,
        $requiredLicenses,
        $licensesToBuy,
        $amountDue,
      )
      as $page
    ) {
      $pdf->addPage($page);
    }

    return [
      'invoice_no' => $invoiceNo,
      'file_name' => sanitizeFilename("{$invoiceNo}.pdf"),
      'content' => $pdf->output(),
    ];
  }

  private function invoicePages(
    Institution $institution,
    Collection $events,
    string $invoiceNo,
    float $licenseCost,
    int $currentLicenses,
    int $requiredLicenses,
    int $licensesToBuy,
    float $amountDue,
  ): array {
    $tableLeft = 48;
    $tableRight = 548;
    $descriptionX = 58;
    $qtyX = 342;
    $unitX = 395;
    $amountX = 475;
    $col1 = 330;
    $col2 = 382;
    $col3 = 462;

    $lines = [
      $this->line('INVOICE', 48, 792, 24, true),
      $this->line(config('app.name'), 48, 764, 14, true),
      $this->line('Billing Department', 48, 746),
      // $this->line('Address: [Company billing address]', 48, 731),
      // $this->line(
      //   'Email: [billing@example.com]  Phone: [company phone]',
      //   48,
      //   716,
      // ),
      $this->line("Invoice No: {$invoiceNo}", 350, 764, 11, true),
      $this->line('Issue Date: ' . now()->format('M d, Y'), 350, 746),
      $this->line('Due Date: Due on receipt', 350, 731),
      $this->line('Currency: NGN', 350, 716),
      $this->line('Bill To', 48, 680, 13, true),
      $this->line($institution->name, 48, 662, 11, true),
      $this->line("Institution Code: {$institution->code}", 48, 647),
      $this->line(
        'Address: ' . ($institution->address ?: '[Institution address]'),
        48,
        632,
      ),
      $this->line(
        'Email: ' . ($institution->email ?: '[Institution email]'),
        48,
        617,
      ),
      $this->line(
        'Phone: ' . ($institution->phone ?: '[Institution phone]'),
        48,
        602,
      ),
      $this->line('Invoice Summary', 48, 570, 13, true),
      $this->line('Description', $descriptionX, 545, 10, true),
      $this->line('Qty', $qtyX, 545, 10, true),
      $this->line('Unit Price', $unitX, 545, 10, true),
      $this->line('Amount', $amountX, 545, 10, true),
      $this->hline($tableLeft, $tableRight, 560, 0.8),
      $this->hline($tableLeft, $tableRight, 532, 0.8),
      $this->vline($tableLeft, 532, 560, 0.8),
      $this->vline($col1, 532, 560, 0.8),
      $this->vline($col2, 532, 560, 0.8),
      $this->vline($col3, 532, 560, 0.8),
      $this->vline($tableRight, 532, 560, 0.8),
    ];

    $y = 514;
    $pages = [];
    if ($events->isEmpty()) {
      $rowTop = 532;
      $rowBottom = 502;
      $lines[] = $this->line('No unactivated exams found.', $descriptionX, 514);
      $lines = array_merge(
        $lines,
        $this->tableRowLines(
          $tableLeft,
          $tableRight,
          $col1,
          $col2,
          $col3,
          $rowBottom,
          $rowTop,
        ),
      );
      $y = 490;
    }

    foreach ($events as $event) {
      if ($y < 120) {
        $pages[] = $lines;
        $lines = [
          $this->line('INVOICE CONTINUED', 48, 792, 18, true),
          $this->line("Invoice No: {$invoiceNo}", 48, 766),
          $this->line('Description', $descriptionX, 730, 10, true),
          $this->line('Qty', $qtyX, 730, 10, true),
          $this->line('Unit Price', $unitX, 730, 10, true),
          $this->line('Amount', $amountX, 730, 10, true),
          $this->hline($tableLeft, $tableRight, 745, 0.8),
          $this->hline($tableLeft, $tableRight, 717, 0.8),
          $this->vline($tableLeft, 717, 745, 0.8),
          $this->vline($col1, 717, 745, 0.8),
          $this->vline($col2, 717, 745, 0.8),
          $this->vline($col3, 717, 745, 0.8),
          $this->vline($tableRight, 717, 745, 0.8),
        ];
        $y = 699;
      }

      $rowTop = $y + 18;
      $rowBottom = $y - 10;
      $quantity = (int) $event->unactivated_exams_count;
      $lines[] = $this->line(
        $this->shortText("Activation licenses - {$event->title}", 58),
        $descriptionX,
        $y,
      );
      $lines[] = $this->line((string) $quantity, $qtyX, $y);
      $lines[] = $this->line($this->money($licenseCost), $unitX, $y);
      $lines[] = $this->line(
        $this->money($quantity * $licenseCost),
        $amountX,
        $y,
      );
      $lines = array_merge(
        $lines,
        $this->tableRowLines(
          $tableLeft,
          $tableRight,
          $col1,
          $col2,
          $col3,
          $rowBottom,
          $rowTop,
        ),
      );
      $y -= 28;
    }

    $y -= 8;
    $summaryLines = [
      ['Licenses needed', $requiredLicenses],
      ['Current licenses', $currentLicenses],
      ['Licenses to buy', $licensesToBuy],
      ['Total amount due', $this->money($amountDue)],
    ];

    foreach ($summaryLines as [$label, $value]) {
      if ($y < 120) {
        $pages[] = $lines;
        $lines = [$this->line('INVOICE SUMMARY', 48, 792, 18, true)];
        $y = 760;
      }
      $lines[] = $this->line(
        $label,
        350,
        $y,
        10,
        $label === 'Total amount due',
      );
      $lines[] = $this->line(
        (string) $value,
        475,
        $y,
        10,
        $label === 'Total amount due',
      );
      $y -= 17;
    }

    $y -= 12;
    $lines[] = $this->line('Notes', 48, $y, 12, true);
    $y -= 17;
    $lines[] = $this->line(
      'This invoice is for activation licenses required for currently unactivated exams.',
      48,
      $y,
    );
    $bankAccounts = config('app.bank-accounts');
    $y -= 15;
    $lines[] = $this->line('Payment instructions: ', 48, $y, 12, true);
    $y -= 15;
    foreach ($bankAccounts as $bankAccount) {
      $lines[] = $this->line('Bank Name: ' . $bankAccount['bank_name'], 48, $y);
      $y -= 12;
      $lines[] = $this->line(
        'Account Number: ' . $bankAccount['account_number'],
        48,
        $y,
      );
      $y -= 12;
      $lines[] = $this->line(
        'Account Name: ' . $bankAccount['account_name'],
        48,
        $y,
      );
      $y -= 15;
    }
    $lines[] = $this->line('or fund licenses online.', 48, $y);

    $pages[] = $lines;
    return $pages;
  }

  private function line(
    string $text,
    int $x,
    int $y,
    int $size = 10,
    bool $bold = false,
  ): array {
    return compact('text', 'x', 'y', 'size', 'bold');
  }

  private function hline(int $x1, int $x2, int $y, float $width = 0.5): array
  {
    return [
      'type' => 'line',
      'x1' => $x1,
      'y1' => $y,
      'x2' => $x2,
      'y2' => $y,
      'width' => $width,
    ];
  }

  private function vline(int $x, int $y1, int $y2, float $width = 0.5): array
  {
    return [
      'type' => 'line',
      'x1' => $x,
      'y1' => $y1,
      'x2' => $x,
      'y2' => $y2,
      'width' => $width,
    ];
  }

  private function tableRowLines(
    int $left,
    int $right,
    int $col1,
    int $col2,
    int $col3,
    int $bottom,
    int $top,
  ): array {
    return [
      $this->hline($left, $right, $bottom, 0.5),
      $this->vline($left, $bottom, $top, 0.5),
      $this->vline($col1, $bottom, $top, 0.5),
      $this->vline($col2, $bottom, $top, 0.5),
      $this->vline($col3, $bottom, $top, 0.5),
      $this->vline($right, $bottom, $top, 0.5),
    ];
  }

  private function money(float $amount): string
  {
    return number_format($amount, 2);
  }

  private function shortText(string $text, int $length): string
  {
    return strlen($text) > $length
      ? substr($text, 0, $length - 3) . '...'
      : $text;
  }
}
