<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\InstitutionUser;
use App\Models\Institution;

class VerifyInstitutionUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = currentUser();

        /** @var Institution $institution */
        $institution = $request->route('institution');

        $institutionUser = InstitutionUser::where('user_id', $user->id)
            ->where('institution_id', $institution->id)
            ->first();

        if (!$institutionUser && !$user->isAdmin()) {
            $message = 'You are not authorized to access this page.';
            return $request->expectsJson()
                ? abort(403, $message)
                : Redirect::guest(URL::route('login'))->with('error', $message);
        }

        if ($request->method() === 'GET') {
            View::share('institution', $institution);
        }

        $request->merge(['institution_id' => $institution->id]);

        return $next($request);
    }
}
