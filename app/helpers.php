<?php

use App\DTO\BreadCrumb;
use App\Models\Institution;
use App\Models\InstitutionUser;
use App\Models\User;
use App\Support\Res;
use Illuminate\Support\Facades\DB;

if (!function_exists('currentUser')) {
  function currentUser(): User|null
  {
    /** @var User */
    $user = auth()->user();
    return $user;
  }
}

if (!function_exists('randomDigits')) {
  function randomDigits($num = 11): string
  {
    $str = '';
    for ($i = 0; $i < $num; $i++) {
      $str .= '#';
    }
    return fake()->numerify($str);
  }
}

if (!function_exists('pageTitle')) {
  function pageTitle($page, bool $appendSiteTitle = true): string
  {
    return $page . ($appendSiteTitle ? ' | ' . config('app.name') : '');
  }
}

if (!function_exists('addSpacing')) {
  function addSpacing($str, $len = 4): string
  {
    return chunk_split($str, $len, ' ');
  }
}

if (!function_exists('failRes')) {
  function failRes($message, array $data = []): Res
  {
    return new Res(['success' => false, 'message' => $message, ...$data]);
  }
}

if (!function_exists('successRes')) {
  function successRes($message = '', array $data = []): Res
  {
    return new Res(['success' => true, 'message' => $message, ...$data]);
  }
}

if (!function_exists('error')) {
  function error($errors, $field): string
  {
    return $errors->has($field)
      ? "<span class=\"invalid-feedback\" role=\"alert\"><strong>" .
          $errors->first($field) .
          '</strong></span>'
      : '';
  }
}

if (!function_exists('je')) {
  /** Helper for json encode with pretty print */
  function je($data): string
  {
    return json_encode($data, JSON_PRETTY_PRINT);
  }
}

if (!function_exists('dlog')) {
  /** Helper to log data using json encode with pretty print */
  function dlog($data)
  {
    info(json_encode($data, JSON_PRETTY_PRINT));
  }
}

if (!function_exists('paginateFromRequest')) {
  function paginateFromRequest(
    $query,
    $defaultPerPage = 100,
  ): \Illuminate\Contracts\Pagination\LengthAwarePaginator {
    $perPage = request()->query('perPage', $defaultPerPage);
    $page = request()->query('page');

    return $query->paginate(perPage: (int) $perPage, page: (int) $page);
  }
}

if (!function_exists('currentInstitution')) {
  function currentInstitution(): Institution|null
  {
    $institution = request()->route('institution');
    if (!($institution instanceof Institution)) {
      return null;
    }
    return $institution;
  }
}

if (!function_exists('currentInstitutionUser')) {
  function currentInstitutionUser(): InstitutionUser|null
  {
    return currentInstitution()?->institutionUsers?->first();
  }
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

if (!function_exists('breadCrumb')) {
  function breadCrumb(
    string $title,
    string $route = '',
    string $icon = '',
    bool $active = false,
  ) {
    return new BreadCrumb($title, $route, $icon, $active);
  }
}

if (!function_exists('sanitizeFilename')) {
  function sanitizeFilename(string $filename): string
  {
    $filename = basename($filename);
    $sanitized = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    return $extension ? "{$sanitized}.{$extension}" : $sanitized;
  }
}
