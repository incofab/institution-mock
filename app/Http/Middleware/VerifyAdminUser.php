<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class VerifyAdminUser
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
        if (!$user) {
            $message = 'You are not authorized to access this page.';
            return $request->expectsJson()
                ? abort(403, $message)
                : Redirect::guest(URL::route('login'))->with('error', $message);
        }
        return $next($request);
    }
}
