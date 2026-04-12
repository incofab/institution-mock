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

    $institutionUsers = InstitutionUser::where('user_id', $user->id)
      ->with('institution')
      ->get();

    if ($institutionUsers->isEmpty()) {
      return view('user.index', []);
    }

    if ($institutionUsers->count() === 1) {
      return redirect(
        route(
          'institutions.dashboard',
          $institutionUsers->first()->institution,
        ),
      );
    }

    return redirect(route('users.institutions.select'));
  }

  function selectInstitution(Request $request)
  {
    $institutionUsers = currentUser()
      ->institutionUsers()
      ->with('institution')
      ->get();

    if ($institutionUsers->isEmpty()) {
      return redirect(route('users.dashboard'));
    }

    if ($institutionUsers->count() === 1) {
      return redirect(
        route(
          'institutions.dashboard',
          $institutionUsers->first()->institution,
        ),
      );
    }

    return view('user.institution-select', [
      'institutionUsers' => $institutionUsers,
      'selectedInstitution' => $this->selectedInstitutionFromRequest(
        $request,
        $institutionUsers,
      ),
    ]);
  }

  function switchInstitution(Request $request)
  {
    $data = $request->validate([
      'institution_id' => ['required', 'integer'],
    ]);

    $institutionUser = currentUser()
      ->institutionUsers()
      ->with('institution')
      ->where('institution_id', $data['institution_id'])
      ->first();

    abort_unless($institutionUser, 403, 'Institution not found for this user');

    return redirect(
      route('institutions.dashboard', $institutionUser->institution),
    )->with('message', "Switched to {$institutionUser->institution->name}");
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

  private function selectedInstitutionFromRequest(
    Request $request,
    $institutionUsers,
  ): Institution|null {
    $selectedInstitution = $request->query('selected_institution');

    if (!$selectedInstitution) {
      return null;
    }

    return $institutionUsers
      ->map(
        fn(InstitutionUser $institutionUser) => $institutionUser->institution,
      )
      ->first(
        fn(Institution $institution) => $institution->code ==
          $selectedInstitution || $institution->id == $selectedInstitution,
      );
  }
}
