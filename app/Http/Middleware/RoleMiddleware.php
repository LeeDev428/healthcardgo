<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->status === 'pending') {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is pending approval. Please contact System Administrator.']);
        }

        if ($user->status !== 'active') {
            Auth::logout();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is not active. Please contact System Administrator for support.']);
        }

        if (! $user->hasAnyRole($roles)) {
            abort(403, 'You do not have the required permissions to access this resource.');
        }

        return $next($request);
    }
}
