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
   * @param bool $forStart indentifies if this is for starting or resuming an exam
   * @return array {success: bool, message: string}
   */
  function syncExamFile(Exam $exam, $forStart = true)
  {
    $contentRes = $this->getContent($exam->event_id, $exam->exam_no, $forStart);

    if ($forStart) {
      if (!$contentRes['success'] && empty($contentRes['exam_not_found'])) {
        return $contentRes;
      }
    }

    $examFileContent = $contentRes['content'] ?? null;

    // $file = $this->getFullFilepath($exam->event_id, $exam->exam_no);
    // $examFileContent = file_exists($file)
    //   ? json_decode(file_get_contents($file), true)
    //   : null;

    $examData = $exam->only(
      'event_id',
      'student_id',
      'num_of_questions',
      'score',
      'status',
      'start_time',
      'pause_time',
      'end_time',
    );
    // If it's not empty, then the exam has just been restarted
    $examFileContent = $contentRes['content'] ?? ['attempts' => []];
    $examFileContent['exam'] = $examData;
    // if (empty($examFileContent)) {
    //   $examFileContent = [
    //     'exam' => $examData,
    //     'attempts' => [],
    //   ];
    // } else {
    //   $examFileContent['exam'] = $examData;
    // }

    $ret = $this->saveFile($contentRes['file'], $examFileContent);

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

    foreach ($studentAttempts as $questionId => $studentAttempt) {
      $savedAttempts[$questionId] = $studentAttempt;
    }

    $examFileContent['attempts'] = $savedAttempts;

    $ret = $this->saveFile($file, $examFileContent);

    return [
      'success' => boolval($ret),
      'message' => $ret ? 'Attempt recorded' : 'Error recording attempt',
    ];
  }

  function endExam($eventId, $examNo)
  {
    $content = $this->getContent($eventId, $examNo, false);

    if (!$content['success']) {
      return $content;
    }

    $examFileContent = $content['content'];
    $file = $content['file'];
    $examFileContent['exam']['status'] = 'ended';
    $examFileContent['exam']['end_time'] = date('d-m-Y H:m:s');

    $ret = $this->saveFile($file, $examFileContent);

    return [
      'success' => boolval($ret),
      'message' => $ret ? 'Exam ended' : 'Error ending exam',
    ];
  }

  private function saveFile($filename, $content)
  {
    return file_put_contents(
      $filename,
      json_encode($content, JSON_PRETTY_PRINT),
    );
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
        'file' => $file,
      ];
    }

    $examTrackContent = json_decode(file_get_contents($file), true);

    if (empty($examTrackContent)) {
      return [
        'success' => false,
        'message' => 'Exam file not found',
        'exam_not_found' => true,
        'file' => $file,
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
          'file' => $file,
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
