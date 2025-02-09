<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\InstitutionUser;

class UserController extends Controller
{
  function index()
  {
    $user = currentUser();

    if ($user->isAdmin()) {
      return redirect(route('admin.dashboard'));
    }

    $institutionUser = InstitutionUser::whereUser_id($user->id)->first();

    if ($institutionUser) {
      return redirect(route('institutions.dashboard', $institutionUser->code));
    }
    return view('user.index', []);
  }
}
