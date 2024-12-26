<?php

require_once 'public/exam-route/exam-route-base.php';
require_once 'app/Helpers/ExamHandler.php';

$examHandler = new \App\Helpers\ExamHandler();

$input = @file_get_contents('php://input');
$post = json_decode($input, true);
$eventId = $post['event_id'];
$examNo = $post['exam_no'];

//     dlog_22($post);
$allAttempts = $post['attempts'];
//     dlog_22($allAttempts);

$ret = $examHandler->attemptQuestion($allAttempts, $eventId, $examNo);

//     dlog_22($ret);

if (!$ret['success']) {
  emitResponse($ret);
}

emitResponse([
  'success' => true,
  'data' => ['success' => array_values($allAttempts), 'failure' => []],
]);
