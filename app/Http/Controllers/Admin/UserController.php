<?php
namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseAdminController
{
    function index()
    {
        $query = User::latest();
        return view('admin.users.index', [
            'allRecords' => paginateFromRequest($query),
        ]);
    }

    function search(Request $request)
    {
        $param = $request->input('search_user');
        $query = User::where('name', 'LIKE', "%$param%")
            ->orWhere('email', 'LIKE', "%$param%")
            ->orWhere('phone', 'LIKE', "%$param%")
            ->latest('id');

        return view('admin.users.index', [
            'allRecords' => paginateFromRequest($query),
        ]);
    }

    function show(User $user)
    {
        return view('admin.users.show', ['userData' => $user]);
    }

    function destroy(User $user)
    {
        $user->delete();
        return redirect(route('admin.users.index'))->with(
            'message',
            'User record deleted',
        );
    }
}
