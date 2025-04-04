<?php
namespace App\Helpers;

use App\Models\Exam;
use App\Support\ExamProcess;

class ExamHandler
{
  const EXAM_TIME_ALLOWANCE = 100; // 100 seconds
  const EXAM_FILES_DIR = __DIR__ . '/../../public/exams/';
  const EXAM_FILE_EXT = 'edr';

  function __construct()
  {
  }

  static function make(): self
  {
    return app()->make(self::class);
    // return new self();
  }

  /**
   * This creates an exam file if it doesn't exits or updates it
   * @param \App\Models\Exam $exam
   * @param bool $forStart indentifies if this is for starting or resuming an exam
   */
  function syncExamFile(Exam $exam, $forStart = true): ExamProcess
  {
    $contentRes = $this->getContent($exam->exam_no, $forStart);

    if ($forStart) {
      if ($contentRes->isNotSuccessful() && !$contentRes->getExamNotFound()) {
        return $contentRes;
      }
    }

    $examFileContent = $contentRes->getExamTrack();
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
    $examFileContent = $contentRes->getExamTrack() ?? [];
    $examFileContent['exam'] = $examData;
    $examFileContent['attempts'] =
      $examFileContent['attempts'] ?? ($exam->attempts ?? []);
    // info($exam->toArray());
    // info($examData);
    $ret = $this->saveFile($contentRes->getFile(), $examFileContent);

    return new ExamProcess(
      boolval($ret),
      $ret ? 'Exam file ready' : 'Exam file failed to create',
    );
  }

  function attemptQuestion(array $studentAttempts, $examNo): ExamProcess
  {
    $content = $this->getContent($examNo);

    if ($content->isNotSuccessful()) {
      return $content;
    }

    $examFileContent = $content->getExamTrack();
    $file = $content->getFile();
    $savedAttempts = $examFileContent['attempts'];

    foreach ($studentAttempts as $questionId => $studentAttempt) {
      $savedAttempts[$questionId] = $studentAttempt;
    }

    $examFileContent['attempts'] = $savedAttempts;

    $ret = $this->saveFile($file, $examFileContent);

    return new ExamProcess(
      boolval($ret),
      $ret ? 'Attempt recorded' : 'Error recording attempt',
    );
  }

  function endExam($examNo): ExamProcess
  {
    $content = $this->getContent($examNo, false);

    if ($content->isNotSuccessful()) {
      return $content;
    }

    $examFileContent = $content->getExamTrack();
    $file = $content->getFile();
    $examFileContent['exam']['status'] = 'ended';
    $examFileContent['exam']['end_time'] = date('d-m-Y H:m:s');

    $ret = $this->saveFile($file, $examFileContent);

    return new ExamProcess(
      boolval($ret),
      $ret ? 'Exam ended' : 'Error ending exam',
    );
  }

  /** Wasn't made private to allow for testing (Mocking) */
  function saveFile($filename, $content)
  {
    return file_put_contents(
      $filename,
      json_encode($content, JSON_PRETTY_PRINT),
    );
  }

  /**
   * @param Collection<int, \App\Models\Question> $questions
   */
  function calculateScoreFromFile(Exam $exam, $questions): ExamProcess
  {
    $ret = $this->getContent($exam->exam_no, false);

    if ($ret->isNotSuccessful()) {
      return $ret;
    }

    $size = $questions->count();
    $examFileContent = $ret->getExamTrack();

    if (empty($exam->attempts)) {
      if (empty($examFileContent) || empty($examFileContent['attempts'])) {
        return ExamProcess::success()->score(0)->numOfQuestions($size);
      }
    }

    $score = 0;
    $attempts = $examFileContent['attempts'] ?? $exam->attempts;
    foreach ($questions as $question) {
      $attempt = $attempts[$question->id] ?? '';
      if ($question->answer === $attempt) {
        $score++;
      }
    }
    return ExamProcess::success()->score($score)->numOfQuestions($size);
  }

  /** Wasn't made private to allow for testing (Mocking) */
  function getFullFilepath($examNo)
  {
    return self::EXAM_FILES_DIR . "exam_$examNo." . self::EXAM_FILE_EXT;
  }

  function getContent($examNo, $checkTime = true): ExamProcess
  {
    $file = $this->getFullFilepath($examNo);

    if (!file_exists($file)) {
      return ExamProcess::fail('Exam file not found')
        ->examNotFound()
        ->file($file);
    }

    $examTrackContent = $this->getExamTrack($file);

    if (empty($examTrackContent)) {
      return ExamProcess::fail('Exam file not found')
        ->examNotFound()
        ->file($file);
    }
    /************Check Exam Time**************/
    if ($checkTime) {
      $exam = $examTrackContent['exam'];
      $currentTime = time();
      $endTime = strtotime($exam['end_time']); // + self::EXAM_TIME_ALLOWANCE;
      $isEnded = ($exam['status'] ?? '') === 'ended';
      // info([
      //   'crrentTime' => $currentTime,
      //   'endTime' => $endTime,
      //   'isEnded' => $isEnded,
      //   'curent > end time' => $currentTime > $endTime,
      // ]);
      // info($exam);
      if ($currentTime > $endTime || $isEnded) {
        return ExamProcess::fail('Time Elapsed/Exam ended')
          ->timeElapsed()
          ->examTrack($examTrackContent)
          ->file($file);
      }
    }
    /*//***********Check Exam Time**************/
    return ExamProcess::success()->examTrack($examTrackContent)->file($file);
  }

  /**
   * @return array {
   *  exam: App\Models\Exam,
   *  attempts: array {
   *    question_id: attempt
   *  }
   * }
   */
  function getExamTrack($file)
  {
    return json_decode(@file_get_contents($file), true);
  }
  function getExamFileData($examNo)
  {
    return $this->getExamTrack($this->getFullFilepath($examNo))['exam'] ?? null;
  }
}
