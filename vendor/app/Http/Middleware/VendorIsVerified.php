<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class VendorIsVerified
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
        $vendor = auth('api')->user();

        if ($vendor->email_verified_at) {
            return $next($request);
        }

        return response()->json([
            'data'      => [],
            'success'   => false,
            'message'   => 'Vendor is not yet verified'
        ], 403);
    }
}
