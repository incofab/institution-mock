<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use App\Support\Res;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  // protected $page = 1;
  // protected $lastIndex = 0;
  // protected $numPerPage = 100;

  // function __construct()
  // {
  //   $this->page = Arr::get($_GET, 'page', 1);
  // }

  function redirect($redirect, $ret)
  {
    return $redirect
      ->with($ret['success'] ? 'error' : 'success', $ret['message'])
      ->withInput()
      ->withErrors(Arr::get($ret, 'val'));
  }

  function apiRes(
    bool $success,
    string $message,
    $data = [],
    $httpStatusCode = 200,
  ) {
    $arr = [
      'success' => $success,
      // 'ok' => $success,
      'message' => $message,
      'data' => $data,
    ];
    return response()->json($arr, $httpStatusCode);
  }

  function apiSuccessRes($data, $message = '')
  {
    return $this->apiRes(true, $message, $data);
  }

  function apiFailRes($data, $message = '')
  {
    return $this->apiRes(false, $message, $data, 401);
  }

  protected function res(
    Res $res,
    string $successRoute = null,
    $failureRoute = null,
  ) {
    if ($res->success && $successRoute) {
      $obj = $successRoute ? redirect($successRoute) : redirect()->back();
      return $obj->with('message', $res->message);
    }

    $obj = $failureRoute ? redirect($failureRoute) : redirect()->back();

    return $obj->with('error', $res->message)->withInput();
  }
}
