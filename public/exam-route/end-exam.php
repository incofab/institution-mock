<?php
require_once 'exam-route-base.php';

$examHandler = new \App\Helpers\ExamHandler();

$input = @file_get_contents('php://input');
$post = json_decode($input, true);
$eventId = $post['event_id'] ?? null;
$examNo = $post['exam_no'] ?? null;

$ret = $examHandler->endExam($examNo);

if ($ret->isNotSuccessful()) {
  emitResponse($ret);
}

emitResponse([
  'success' => true,
  'message' => 'Exam ended successfully',
]);
