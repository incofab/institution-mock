<?php
require_once 'exam-route-base.php';
require_once APP_DIR . 'Helpers/ExamHandler.php';

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
  'message' => 'Exam ended successfully',
]);
