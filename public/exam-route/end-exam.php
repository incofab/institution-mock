<?php

require_once 'public/exam-route/exam-route-base.php';
require_once 'app/Helpers/ExamHandler.php';

$examHandler = new \App\Helpers\ExamHandler();

$input = @file_get_contents('php://input');
$post = json_decode($input, true);
$eventId = $post['event_id'];
$examNo = $post['exam_no'];

$ret = $examHandler->endExam($eventId, $examNo);

if (!$ret['success']) {
  emitResponse($ret);
}

emitResponse([
  'success' => true,
  'data' => ['success' => array_values($allAttempts), 'failure' => []],
]);
