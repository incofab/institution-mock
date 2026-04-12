<?php

namespace App\Support;

class SimplePdf
{
  private array $pages = [];

  function __construct(private string $title)
  {
  }

  function addPage(array $lines): void
  {
    $this->pages[] = $lines;
  }

  function output(): string
  {
    $objects = [];
    $pagesObjectId = 2;
    $fontObjectId = 3;
    $boldFontObjectId = 4;
    $pageObjectIds = [];
    $contentObjectIds = [];
    $nextObjectId = 5;

    foreach ($this->pages as $page) {
      $pageObjectIds[] = $nextObjectId++;
      $contentObjectIds[] = $nextObjectId++;
    }

    $objects[1] = '<< /Type /Catalog /Pages 2 0 R >>';
    $kids = collect($pageObjectIds)
      ->map(fn($id) => "{$id} 0 R")
      ->implode(' ');
    $objects[$pagesObjectId] =
      "<< /Type /Pages /Kids [ {$kids} ] /Count " .
      count($pageObjectIds) .
      ' >>';
    $objects[$fontObjectId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
    $objects[$boldFontObjectId] =
      '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

    foreach ($pageObjectIds as $index => $pageObjectId) {
      $contentObjectId = $contentObjectIds[$index];
      $objects[$pageObjectId] =
        "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 {$fontObjectId} 0 R /F2 {$boldFontObjectId} 0 R >> >> /Contents {$contentObjectId} 0 R >>";

      $content = $this->contentStream($this->pages[$index]);
      $objects[$contentObjectId] =
        "<< /Length " .
        strlen($content) .
        " >>\nstream\n{$content}\nendstream";
    }

    ksort($objects);

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $id => $object) {
      $offsets[$id] = strlen($pdf);
      $pdf .= "{$id} 0 obj\n{$object}\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($id = 1; $id <= count($objects); $id++) {
      $pdf .= sprintf("%010d 00000 n \n", $offsets[$id]);
    }

    $pdf .=
      "trailer\n<< /Size " .
      (count($objects) + 1) .
      " /Root 1 0 R /Info << /Title ({$this->escape($this->title)}) >> >>\nstartxref\n{$xrefOffset}\n%%EOF";

    return $pdf;
  }

  private function contentStream(array $lines): string
  {
    return collect($lines)
      ->map(function ($line) {
        if (($line['type'] ?? null) === 'line') {
          $x1 = $line['x1'];
          $y1 = $line['y1'];
          $x2 = $line['x2'];
          $y2 = $line['y2'];
          $width = $line['width'] ?? 0.5;

          return "q {$width} w {$x1} {$y1} m {$x2} {$y2} l S Q";
        }

        $font = ($line['bold'] ?? false) ? 'F2' : 'F1';
        $size = $line['size'] ?? 10;
        $x = $line['x'] ?? 48;
        $y = $line['y'] ?? 780;
        $text = $this->escape($line['text'] ?? '');

        return "BT /{$font} {$size} Tf {$x} {$y} Td ({$text}) Tj ET";
      })
      ->implode("\n");
  }

  private function escape(string $text): string
  {
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
  }
}
