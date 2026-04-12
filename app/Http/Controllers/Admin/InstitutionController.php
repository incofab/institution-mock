<?php
namespace App\Http\Controllers\Admin;

use App\Actions\CreditLicenseFunding;
use App\Enums\InstitutionUserRole;
use Illuminate\Http\Request;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Validation\Rule;

class InstitutionController extends BaseAdminController
{
  function index()
  {
    $query = Institution::query()->latest();
    return view('admin.institutions.index', [
      'allRecords' => paginateFromRequest($query),
    ]);
  }

  function create()
  {
    return view('admin.institutions.create');
  }

  function store(Request $request)
  {
    $data = $request->validate(Institution::ruleCreate());
    Institution::create([
      ...$data,
      'created_by_user_id' => currentUser()->id,
      'code' => Institution::generateInstitutionCode(),
    ]);

    return redirect(route('admin.institutions.index'))->with(
      'message',
      'Record created successfully',
    );
  }

  function edit(Request $request, Institution $institution)
  {
    return view('admin.institutions.create', ['edit' => $institution]);
  }

  function update(Request $request, Institution $institution)
  {
    $data = $request->validate(Institution::ruleCreate());
    $institution->fill($data)->save();

    return redirect(route('admin.institutions.index'))->with(
      'message',
      'Record updated',
    );
  }

  function destroy(Institution $institution)
  {
    abort_if(
      $institution->courses()->exists() || $institution->events()->exists(),
      401,
      'This institution cannot be deleted',
    );
    $institution->delete();
    return redirect(route('admin.institutions.index'))->with(
      'message',
      'Delete institution',
    );
  }

  function assignUserView(Institution $institution)
  {
    return view('admin.institutions.assign-user', [
      'institution' => $institution,
    ]);
  }

  function assignUserStore(Request $request, Institution $institution)
  {
    $data = $request->validate([
      'email' => ['required', 'exists:users,email'],
      'role' => ['required', Rule::in(InstitutionUserRole::values())],
    ]);

    $user = User::where('email', $data['email'])->firstOrFail();
    $institution->institutionUsers()->updateOrCreate(
      ['user_id' => $user->id],
      ['role' => $data['role']],
    );

    return redirect(route('admin.institutions.index'))->with(
      'message',
      'User assigned',
    );
  }

  function fundView(Institution $institution)
  {
    $this->authorizeFunding();

    return view('admin.institutions.fund', [
      'institution' => $institution,
      'fundings' => $institution
        ->fundings()
        ->with('user')
        ->latest()
        ->limit(20)
        ->get(),
    ]);
  }

  function fundingHistory()
  {
    $this->authorizeFunding();

    return view('admin.institutions.funding-history', [
      'allRecords' => paginateFromRequest(
        Funding::query()
          ->with('institution', 'user')
          ->latest('id'),
      ),
    ]);
  }

  function fundStore(Request $request, Institution $institution)
  {
    $this->authorizeFunding();

    $data = $request->validate([
      'amount' => ['required', 'numeric', 'min:0.01'],
    ]);

    (new CreditLicenseFunding())->runForAmount(
      $institution,
      currentUser(),
      (float) $data['amount'],
    );

    return redirect(route('admin.institutions.index'))->with(
      'message',
      'Institution licenses funded successfully',
    );
  }

  private function authorizeFunding()
  {
    abort_unless(currentUser()?->isAdmin(), 403);
  }
}
