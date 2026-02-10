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
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'กรุณาเข้าสู่ระบบ');
        }

        foreach ($roles as $role) {
            if ($role === 'admin' && $user->isAdmin()) {
                return $next($request);
            }
            if ($role === 'tenant' && $user->isTenant()) {
                return $next($request);
            }
            if ($role === 'staff' && $user->isStaff()) {
                return $next($request);
            }
        }

        // If user has tenant role but trying to access admin routes, redirect to tenant dashboard
        if ($user->isTenant()) {
            return redirect()->route('tenant.dashboard')->with('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        // Otherwise redirect to login
        return redirect()->route('login')->with('error', 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
    }
}
