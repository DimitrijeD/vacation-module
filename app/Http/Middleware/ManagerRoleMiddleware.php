<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ManagerRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->role === \App\Models\User::ROLE_MANAGER){
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthorized access',
        ], 401);
    }
}
