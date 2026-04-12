<?php
namespace App\Http\Controllers\User;

use App\Enums\InstitutionUserRole;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionUser;
use Illuminate\Http\Request;

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

  function createInstitution()
  {
    return view('user.institution-create');
  }

  function storeInstitution(Request $request)
  {
    $data = $request->validate(Institution::ruleCreate(false));

    $institution = Institution::create([
      ...$data,
      'created_by_user_id' => currentUser()->id,
      'code' => Institution::generateInstitutionCode(),
      'licenses' => 0,
      'license_cost' => Institution::DEFAULT_LICENSE_COST,
    ]);

    $institution->institutionUsers()->create([
      'user_id' => currentUser()->id,
      'role' => InstitutionUserRole::Admin,
    ]);

    return redirect(route('institutions.dashboard', $institution))->with(
      'message',
      'Institution created successfully',
    );
  }
}
