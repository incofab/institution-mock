<?php

namespace App\Http\Controllers\Institutions;

use App\Enums\InstitutionUserRole;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\InstitutionUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstitutionUserController extends Controller
{
  function index(Institution $institution)
  {
    $this->authorizeInstitutionAdmin($institution);

    return view('institutions.users.index', [
      'allRecords' => paginateFromRequest(
        $institution
          ->institutionUsers()
          ->with('user')
          ->latest('id'),
      ),
      'roles' => InstitutionUserRole::cases(),
    ]);
  }

  function store(Request $request, Institution $institution)
  {
    $this->authorizeInstitutionAdmin($institution);

    $data = $request->validate([
      'email' => ['required', 'exists:users,email'],
      'role' => ['required', Rule::in(InstitutionUserRole::values())],
    ]);

    $user = User::query()
      ->where('email', $data['email'])
      ->firstOrFail();

    $institution->institutionUsers()->updateOrCreate(
      ['user_id' => $user->id],
      ['role' => $data['role']],
    );

    return redirect(instRoute('users.index'))->with(
      'message',
      'Institution user saved successfully',
    );
  }

  private function authorizeInstitutionAdmin(Institution $institution): void
  {
    if (currentUser()?->isAdmin()) {
      return;
    }

    $institutionUser = InstitutionUser::query()
      ->where('institution_id', $institution->id)
      ->where('user_id', currentUser()?->id)
      ->first();

    abort_unless($institutionUser?->isAdmin(), 403);
  }
}
