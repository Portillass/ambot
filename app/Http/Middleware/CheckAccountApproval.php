<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Check if user is not super admin and doesn't have a database_name (not approved)
            if (!$user->isSuperAdmin() && !$user->database_name) {
                auth()->logout();
                return redirect()->route('login')->with('error', 'Your account is pending approval. Please wait for the super admin to approve your account.');
            }
        }

        return $next($request);
    }
}
