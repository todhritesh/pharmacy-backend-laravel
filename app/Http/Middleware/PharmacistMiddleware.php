<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PharmacistMiddleware
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
        $role = auth('admin_api')->user()->role;
        if($role != 'pharmacist'){
            return response()->json([
                'message'=>'unauthorized',
            ],401);
        }
        return $next($request);
    }
}
