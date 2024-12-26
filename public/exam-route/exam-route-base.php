<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, origin');
header('Content-Type: application/json; charset=UTF-8');

require_once 'config/a_config.php';

function emitResponse($data)
{
  die(json_encode($data));
}

function dlog_22($msg)
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
    __DIR__ . '/public/errorlog.txt',
  );
}
