<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOfficer
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();
        if (!$user || $user->role !== 'officer' || !$user->is_active) {
            abort(403, 'Access denied. Only active officers can access this area.');
        }

        return $next($request);
    }
}
