<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Arr;
use App\Core\ErrorCodes;

class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  protected $page = 1;
  protected $lastIndex = 0;
  protected $numPerPage = 100;

  function __construct()
  {
    $this->page = Arr::get($_GET, 'page', 1);
  }

  public function view($view, $data = [], $merge = [])
  {
    if (!isset($data['page'])) {
      $data['page'] = $this->page;
    }

    if (!isset($data['numPerPage'])) {
      $data['numPerPage'] = $this->numPerPage;
    }

    return view($view, $data, $merge);
  }

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
      'ok' => $success,
      'message' => $message,
      'data' => $data,
    ];
    return response()->json($arr, $httpStatusCode);
  }

  function apiSuccessRes($data, $message = '', array $extraData = [])
  {
    return $this->apiRes(true, $message, [...$data, ...$extraData]);
  }

  function apiFailRes($data, $message = '', array $extraData = [])
  {
    return $this->apiRes(false, $message, [...$data, ...$extraData], 401);
  }

  // function emitResponseRet(array $ret){

  //     $ret['error_code'] = $ret['success'] ? ErrorCodes::OK : ErrorCodes::FAILED;

  //     return response()->json($ret);
  // }

  // function apiEmitResponse($data){
  //     return response()->json($data);
  // }
}
