<?php

header('Access-Control-Allow-Origin: *');
header(
  'Access-Control-Allow-Headers: Content-Type, Origin, Authorization, X-Requested-With'
);
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
  http_response_code(200);
  // Return OK for preflight checks
  exit();
}
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
define('APP_DIR', __DIR__ . '/../../app/');

require_once APP_DIR . 'Helpers/ExamHandler.php';
require_once APP_DIR . 'Support/ExamProcess.php';

function emitResponse($data)
{
  echo json_encode($data);
}

function dlog($msg)
{
  $str = '';
  if (is_array($msg)) {
    $str = json_encode($msg, JSON_PRETTY_PRINT);
  } else {
    $str = $msg;
  }

  error_log(
    '*************************************' .
      PHP_EOL .
      '     Date Time: ' .
      date('Y-m-d h:m:s') .
      PHP_EOL .
      '------------------------------------' .
      PHP_EOL .
      $str .
      PHP_EOL .
      PHP_EOL .
      '*************************************' .
      PHP_EOL,
    3,
    __DIR__ . '/public/errorlog.txt'
  );
}
