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

    $institutionUser = InstitutionUser::where('user_id', $user->id)
      ->with('institution')
      ->first();

    if ($institutionUser) {
      return redirect(
        route('institutions.dashboard', $institutionUser->institution),
      );
    }
    return view('user.index', []);
  }
}
