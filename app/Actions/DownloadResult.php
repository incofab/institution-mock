<?php
namespace App\Actions;

use App\Models\Event;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DownloadResult
{
  function __construct(private Event $event)
  {
  }

  public static function run(Event $event): Xlsx
  {
    $obj = new self($event);
    return $obj->execute();
  }

  public function execute(): Xlsx
  {
    $items = [];
    $items[] = [
      'A' => 'Student',
      'B' => 'Subjects',
      'C' => 'Score',
      'D' => 'Exam Score',
      'E' => 'Date',
    ];
    foreach ($this->event->exams as $key => $exam) {
      $subjects = $exam->examCourses
        ->map(
          fn(
            $examCourse,
          ) => "{$examCourse->course_code} = {$examCourse->score}",
        )
        ->toArray();

      $scorePercent = $exam->examCourses->sum(
        fn($item) => $item->scorePercent(),
      );
      $totalQuestion = $exam->examCourses->count() * 100;
      $items[] = [
        'A' => $exam->student?->name,
        'B' => implode(' | ', $subjects),
        'C' => "{$exam->score}/{$exam->num_of_questions}",
        'D' => "{$scorePercent}/$totalQuestion",
        'E' => $exam->created_at->toDateTimeString(),
      ];
    }

    return $this->insert($items, function (Worksheet $workSheet) {
      $workSheet->getColumnDimension('A')->setWidth(50);
      $workSheet->getColumnDimension('B')->setWidth(50);
    });
  }

  private function insert(array $entries, callable $setColumns): Xlsx
  {
    $spreadsheet = new Spreadsheet();
    $workSheet = $spreadsheet->getActiveSheet();
    foreach ($entries as $key => $entry) {
      $row = $key + 1;
      foreach ($entry as $column => $value) {
        $workSheet->setCellValue("{$column}{$row}", $value);
      }
    }

    $setColumns($workSheet);

    return new Xlsx($spreadsheet);
  }
}
