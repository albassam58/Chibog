<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class AdminIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $admin = auth('sanctum')->user();

        if ($admin->email_verified_at) {
            return $next($request);
        }

        return response()->json([
            'data'      => [],
            'success'   => false,
            'message'   => 'Admin is not yet verified'
        ], 403);
    }
}
