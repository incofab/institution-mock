<?php
// To be deleted
// errorhandling();

use Illuminate\Support\Arr;

function redirect_($to)
{
  if (empty($to)) {
    $to = $_SERVER['REQUEST_URI'];
  }

  header("Location: $to");

  exit();
}

function assets($pathAfterPublic, $dontAddVersion = false)
{
  $file = asset((config('app.debug') ? '' : '') . $pathAfterPublic);

  return $file;
}

function getFullAddr($routeName = null, $params = '')
{
  $http = 'http';

  if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
    $http = 'https';
  }

  return "$http://{$_SERVER['HTTP_HOST']}" . getAddr($routeName, $params);
}

function markSelected($option, $value, $stringToReturn = ' selected ')
{
  if ($option == $value) {
    return $stringToReturn;
  }

  return '';
}

/**
 * Removes malicious tags from HTML text
 * @param string $html
 * @return string a filtered html string
 */
function purifyHTML($html)
{
  return $html;
}
/**
 * Get the first value in an array, works even for nested arrays
 * @param $arr
 * @return string
 */
function getFirstValue($arr)
{
  if (!is_array($arr)) {
    return '';
  }

  foreach ($arr as $key => $value) {
    if (is_array($value)) {
      return getFirstValue($value);
    }

    return $value;
  }
  return '';
}

function generateRef($prefix = null, $forHtmlForm = true)
{
  $ref = uniqid($prefix . '-') . '-' . rand(100, 10000);

  if ($forHtmlForm) {
    $ref = "<input type=\"hidden\" name=\"reference\" value=\"$ref\" />";
  }

  return $ref;
}

// function addSpacing($str, $step = 4, $spacingCharacter = ' ', $asArray = false)
// {
//     $count = strlen($str);
//     $arr = [];
//     for ($i = 0; $i < $count; $i = $i+$step)
//     {
//         $arr[] = substr($str, $i, $step);
//     }

//     return $asArray ? $arr : implode($spacingCharacter, $arr);
// }

function cryptPassword($password)
{
  // 	$salt = '$2a$07$' . substr(md5(uniqid(rand(), true)), 0, 22);
  // 	return crypt($password, $salt);
  return password_hash($password, PASSWORD_BCRYPT);
}
/**
 * Compares both passwords
 * @param $hashedPassword
 * @param $userInput
 * @return boolean true if both passwords match, false otherwise
 */
function comparePasswords($hashedPassword, $userInput)
{
  return hash_equals($hashedPassword, crypt($userInput, $hashedPassword));
}

function errorhandling()
{
  if (config('app.debug')) {
    return;
  }
  set_error_handler('myErrorHandler');
  return;
  // 	if(config('app.debug')){
  // 		ini_set("display_errors" , true );
  // 		ini_set('display_start_up_errors', true);
  // 		error_reporting(E_ERROR);
  // 	}else{
  // 		set_error_handler("myErrorHandler");
  // 	}
}

function formatValidationErrors($errorsContainer, $asHTML = false)
{
  if (!is_array($errorsContainer) || empty($errorsContainer)) {
    return [];
  }

  $errorArr = [];
  $html = '<div class="text-left">';
  foreach ($errorsContainer as $errors) {
    foreach ($errors as $error) {
      $errorArr[] = $error;

      $html .= '<p><i class="fa fa-star"></i> ' . $error . '</p>';
    }
  }
  $html .= '</div>';
  return $asHTML ? $html : $errorArr;
}
/**
 * Displays all the validation errors returned by the form validator
 * @param $errors
 */
function showFormValidationErrors($errors)
{
  if (!is_array($errors)) {
    return;
  }
  $str = '<p>';
  foreach ($errors as $error) {
    $str .= "<p class='error alert alert-danger'>$error[0]</p>";
  }
  $str .= '</p><br />';
  echo $str;
}

function parseDateTimeString($dateTimeString)
{
  if (!($dateTimeString instanceof \Carbon\Carbon)) {
    $dateTimeString = \Carbon\Carbon::parse($dateTimeString);
  }

  $str = '';

  if ($dateTimeString->isToday()) {
    $str = 'Today, ' . $dateTimeString->format('H:i:s');
  } elseif ($dateTimeString->isYesterday()) {
    $str = 'Yesterday, ' . $dateTimeString->format('H:i:s');
  } elseif (
    $dateTimeString->timestamp >
    \Carbon\Carbon::now()->previousWeekday()->timestamp
  ) {
    $str = $dateTimeString->format('l, H:i:s');
  } else {
    $str = $dateTimeString->toDateTimeString();
  }
  return $str;
}

/**
 * Format a phone number
 * @param string $phone
 * @param string $removePrefix Prefix to be removed. Eg, phone number country code
 * @return mixed
 */
function formatPhoneNumber($phone)
{
  $phone = trim($phone);

  $phone = str_replace([' ', '-', '+', '_'], '', $phone);

  if (substr($phone, 0, 4) == '2340') {
    $phone = substr($phone, 3);
  }

  if (substr($phone, 0, 3) == '234') {
    $phone = '0' . substr($phone, 3);
  }

  return $phone;
}

/**
 * get access token from header
 * */
function getBearerToken()
{
  /**
   * Get hearder Authorization
   * */
  function getAuthorizationHeader()
  {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
      //Nginx or fast CGI
      $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(
        array_map('ucwords', array_keys($requestHeaders)),
        array_values($requestHeaders),
      );
      //print_r($requestHeaders);
      if (isset($requestHeaders['Authorization'])) {
        $headers = trim($requestHeaders['Authorization']);
      }
    }
    return $headers;
  }

  $headers = getAuthorizationHeader();
  // HEADER: Get the access token from the header
  if (!empty($headers)) {
    if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
      return $matches[1];
    }
  }
  return null;
}

function myErrorHandler($errno, $errstr, $errfile, $errline)
{
  $stackTrace = debug_backtrace();
  // Just take the first 3 in the stack trace
  $stackTrace = array_slice($stackTrace, 0, 3);

  $errorArr = [
    'error No' => $errno,
    'Error Message' => $errstr,
    'File' => $errfile,
    'Line Number' => $errline,
    'Stack Trace' => $stackTrace,
  ];

  dlog($errorArr);

  $msg = [
    'message' => 'A fatal error occured. Please contact administrator',
    'success' => false,
  ];

  switch ($errno) {
    case E_ERROR:
    case E_USER_ERROR:
      exit(json_encode($msg, JSON_PRETTY_PRINT));
      // 			exit("FATAL error $errstr at $errfile:$errline");
      break;
  }
}

function getIP()
{
  $ip = '';
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
    $ip = Arr::get($_SERVER, 'REMOTE_ADDR', '');
  }
  return $ip;
}
function getAddr($routeName = null, $params = '')
{
  return route($routeName, $params);
  $options = '';

  if (is_array($params) && !empty($params)) {
    $options = '/' . implode('/', $params);
  } else {
    $options = $params ? "/$params" : '';
  }

  if (empty($routeName)) {
    $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

    $query = Arr::get($parsedUrl, 'query', '');

    return rtrim($parsedUrl['path'], '/') .
      $options .
      ($query && !$options ? "?$query" : '');
  }

  global $routesArr, $routesArrAPI;

  if (!empty($routesArr[$routeName]) || !empty($routesArrAPI[$routeName])) {
    $routeAddr = !empty($routesArr[$routeName])
      ? $routesArr[$routeName]
      : $routesArrAPI[$routeName];
    //         $parsedUrl = parse_url($routeAddr);
    //         $query = Arr::get($parsedUrl, 'query', '');

    return rtrim(ADDR, '/') . $routeAddr . $options;
    //         return rtrim(ADDR, '/').$parsedUrl['path'].$options.(($query && !$options)?"?$query":'');
  } else {
    throw new Exception("Route: ($routeName) not found in route collections");
  }
}

function downloadContent($fileToDownload, $contentType = 'application/zip')
{
  $file_name = basename($fileToDownload);

  header("Content-Type: $contentType");

  header("Content-Disposition: attachment; filename=$file_name");

  header('Content-Length: ' . filesize($fileToDownload));

  readfile($fileToDownload);
}

function dDie($param)
{
  die(json_encode($param, JSON_PRETTY_PRINT));
}

function dlog($msg)
{
  $str = '';

  if (!is_string($msg)) {
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
    public_path() . '/errorlog.txt',
  );
}

function errorlog($msg)
{
  $str = '';

  if (is_array($msg)) {
    $str = json_encode($msg, JSON_PRETTY_PRINT);
  } else {
    $str = $msg;
  }

  error_log(
    '------------------------------------' .
      PHP_EOL .
      $str .
      PHP_EOL .
      '------------------------------------' .
      PHP_EOL,

    3,
    '../public/errorlog.txt',
  );
}

if (!function_exists('instRoute')) {
  function instRoute($routeSuffix, $moreParam = [], $institution = null)
  {
    $institution = $institution ?? currentInstitution();
    $params = [$institution];
    if (is_array($moreParam)) {
      $params = array_merge($params, $moreParam);
    } else {
      $params[] = $moreParam;
    }
    return route("institutions.{$routeSuffix}", $params);
  }
}
