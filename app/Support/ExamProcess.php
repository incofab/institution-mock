<?php
namespace App\Support;

class ExamProcess
{
  private bool $exam_not_found = false;
  private string $file = '';
  private bool $time_elapsed = false;
  private mixed $exam_track = null;

  private int $num_of_questions = 0;
  private int|float $score = 0;

  function __construct(private bool $success, private string $message)
  {
  }

  public static function success($message = '')
  {
    return new static(true, $message);
  }

  public static function fail($message = '')
  {
    return new static(false, $message);
  }

  function isSuccessful()
  {
    return $this->success;
  }

  function isNotSuccessful()
  {
    return !$this->success;
  }

  function getMessage()
  {
    return $this->message;
  }

  public function getExamNotFound(): bool
  {
    return $this->exam_not_found;
  }

  public function examNotFound(bool $exam_not_found = true): self
  {
    $this->exam_not_found = $exam_not_found;
    return $this;
  }

  public function getFile(): string
  {
    return $this->file;
  }

  public function file(string $file): self
  {
    $this->file = $file;
    return $this;
  }

  public function getTimeElapsed(): bool
  {
    return $this->time_elapsed;
  }

  public function timeElapsed(bool $time_elapsed = true): self
  {
    $this->time_elapsed = $time_elapsed;
    return $this;
  }

  public function hasContent(): bool
  {
    return !empty($this->content);
  }

  /**
   * @return array {
   *  exam: App\Models\Exam,
   *  attempts: array {
   *    question_id: attempt
   *  }
   * }
   */
  public function getExamTrack(): mixed
  {
    return $this->exam_track;
  }

  public function examTrack($examTrack): self
  {
    $this->exam_track = $examTrack;
    return $this;
  }

  public function getNumOfQuestions(): int
  {
    return $this->num_of_questions;
  }

  public function numOfQuestions(int $num_of_questions): self
  {
    $this->num_of_questions = $num_of_questions;
    return $this;
  }

  public function getScore(): int|float
  {
    return $this->score;
  }

  public function score(int|float $score): self
  {
    $this->score = $score;
    return $this;
  }
  function toArray(): array
  {
    return [
      'exam_not_found' => $this->exam_not_found,
      'file' => $this->file,
      'time_elapsed' => $this->time_elapsed,
      'exam_track' => $this->exam_track,
      'num_of_questions' => $this->num_of_questions,
      'score' => $this->score,
      'success' => $this->success,
      'message' => $this->message,
    ];
  }

  // function __get($name): mixed
  // {
  //   return $this->offsetGet($name);
  // }

  // function __set($name, $value): void
  // {
  //   $this->offsetSet($name, $value);
  // }
}
