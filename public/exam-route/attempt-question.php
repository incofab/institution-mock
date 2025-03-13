<?php
require_once 'exam-route-base.php';

$examHandler = new \App\Helpers\ExamHandler();

$input = @file_get_contents('php://input');
$post = json_decode($input, true);
$eventId = $post['event_id'] ?? null;
$examNo = $post['exam_no'] ?? null;

// dlog($post);
$allAttempts = $post['attempts'];

$ret = $examHandler->attemptQuestion($allAttempts, $examNo);

if ($ret->isNotSuccessful()) {
  emitResponse($ret);
  return;
}

emitResponse([
  'success' => true,
  'data' => ['success' => array_values($allAttempts), 'failure' => []],
]);
