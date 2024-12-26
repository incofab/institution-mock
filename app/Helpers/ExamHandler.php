<?php
namespace App\Helpers;

use App\Models\Exam;

class ExamHandler
{
  const EXAM_TIME_ALLOWANCE = 100; // 100 seconds

  function __construct()
  {
  }

  static function make()
  {
    return new self();
  }

  /**
   * This creates an exam file if it doesn't exits or updates it
   * @param \App\Models\Exam $exam
   * @return array {success: bool, message: string}
   */
  function syncExamFile(Exam $exam)
  {
    $file = $this->getFullFilepath($exam->event_id, $exam->exam_no);

    $examFileContent = file_exists($file)
      ? json_decode(file_get_contents($file), true)
      : null;

    // If it's not empty, then the exam has just been restarted
    if (empty($examFileContent)) {
      $examFileContent = ['exam' => $exam->toArray(), 'attempts' => []];
    } else {
      $examFileContent['exam'] = $exam->toArray();
    }

    $ret = file_put_contents(
      $file,
      json_encode($examFileContent, JSON_PRETTY_PRINT),
    );

    return [
      'success' => boolval($ret),
      'message' => $ret ? 'Exam file ready' : 'Exam file failed to create',
    ];
  }

  function attemptQuestion(array $studentAttempts, $eventId, $examNo)
  {
    $content = $this->getContent($eventId, $examNo);

    if (!$content['success']) {
      return $content;
    }

    $examFileContent = $content['content'];
    $file = $content['file'];
    $savedAttempts = $examFileContent['attempts'];

    foreach ($studentAttempts as $studentAttempt) {
      $subjectId = $studentAttempt['exam_course_id'];
      $questionId = $studentAttempt['question_id'];

      if (!isset($savedAttempts[$subjectId])) {
        $savedAttempts[$subjectId] = [];
      }

      $savedAttempts[$subjectId][$questionId] = $studentAttempt;
    }

    $examFileContent['attempts'] = $savedAttempts;

    $ret = file_put_contents(
      $file,
      json_encode($examFileContent, JSON_PRETTY_PRINT),
    );

    return [
      'success' => boolval($ret),
      'message' => $ret ? 'Attempt recorded' : 'Error recording attempt',
    ];
  }

  function endExam($eventId, $examNo)
  {
    $content = $this->getContent($eventId, $examNo);

    if (!$content['success']) {
      return $content;
    }

    $examFileContent = $content['content'];
    $file = $content['file'];
    $examFileContent['exam']['status'] = 'ended';

    $ret = file_put_contents(
      $file,
      json_encode($examFileContent, JSON_PRETTY_PRINT),
    );

    return [
      'success' => boolval($ret),
      'message' => $ret ? 'Exam ended' : 'Error ending exam',
    ];
  }

  function calculateScoreFromFile($exam, $questions)
  {
    $ret = $this->getContent($exam->event_id, $exam->exam_no, false);

    if (!$ret['success']) {
      return $ret;
    }

    $size = count($questions);
    $examFileContent = $ret['content'] ?? null;

    if (empty($examFileContent) || empty($examFileContent['attempts'])) {
      return [
        'success' => true,
        'score' => 0,
        'num_of_questions' => $size,
      ];
    }

    $score = 0;
    $attempts = $examFileContent['attempts'];
    foreach ($questions as $question) {
      $attempt = $attempts[$question->id] ?? '';
      if ($question['answer'] === $attempt) {
        $score++;
      }
    }

    return [
      'success' => true,
      'score' => $score,
      'num_of_questions' => $size,
    ];
  }

  private function getFullFilepath(
    $eventId,
    $examNo,
    $toCreateBaseFolder = true,
  ) {
    $filename = "exam_$examNo";
    $baseFolder = EXAM_FILES_DIR . "event_$eventId";
    if (!file_exists($baseFolder) && $toCreateBaseFolder) {
      mkdir($baseFolder, 0777, true);
    }
    return "$baseFolder/$filename." . EXAM_FILE_EXT;
  }

  function getContent($eventId, $examNo, $checkTime = true)
  {
    $file = $this->getFullFilepath($eventId, $examNo, false);

    if (!file_exists($file)) {
      return [
        'success' => false,
        'message' => 'Exam file not found',
        'exam_not_found' => true,
      ];
    }

    $examTrackContent = json_decode(file_get_contents($file), true);

    if (empty($examTrackContent)) {
      return [
        'success' => false,
        'message' => 'Exam file not found',
        'exam_not_found' => true,
      ];
    }

    /************Check Exam Time**************/
    if ($checkTime) {
      $exam = $examTrackContent['exam'];
      $currentTime = time();
      $endTime = strtotime($exam['end_time']) + self::EXAM_TIME_ALLOWANCE;
      $isEnded = ($exam['status'] ?? '') === 'ended';
      if ($currentTime > $endTime || $isEnded) {
        return [
          'success' => false,
          'message' => 'Time Elapsed/Exam ended',
          'time_elapsed' => true,
          'content' => $examTrackContent,
        ];
      }
    }
    /*//***********Check Exam Time**************/

    return [
      'success' => true,
      'message' => '',
      'content' => $examTrackContent,
      'file' => $file,
    ];
  }
}
