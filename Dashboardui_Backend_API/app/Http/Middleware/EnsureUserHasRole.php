<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * @param  array<int, string>  $roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check() || $roles === []) {
            Auth::logout();

            return redirect(route_with_lang('login'));
        }

        if (! Auth::user()->hasAnyRole($roles)) {
            abort(403, get_translation('unauthorized_role_access'));
        }

        return $next($request);
    }
}
