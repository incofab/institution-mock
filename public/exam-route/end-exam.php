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
  return;
}

$endExamUrl = "/api/$examNo/end-exam";
execCurl($endExamUrl);

emitResponse([
  'success' => true,
  'message' => 'Exam ended successfully',
]);

function execCurl($url, $data = [], $method = 'POST')
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  if (!empty($data)) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
  }
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'content-type: application/json',
    'cache-control: no-cache',
  ]);

  $request = curl_exec($ch);
  $error = curl_error($ch);
  curl_close($ch);
}
