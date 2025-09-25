<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if (!$user || $user->role !== 'super_admin') {
            abort(403, 'Unauthorized. Only super administrators can access this page.');
        }

        return $next($request);
    }
}