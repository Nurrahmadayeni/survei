<?php

namespace App\Http\Middleware;

use App\UserAuth;
use Closure;
use Illuminate\Support\Facades\Auth;

class IsOperator {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $is_super = UserAuth::where('username', $user->username)->where('auth_type', 'SU')->first();
        $is_operator = UserAuth::where('username', $user->username)->where(function ($query)
        {
            $query->where('auth_type', 'OPU')->orWhere('auth_type', 'OPF');
        })->first();
        if (empty($is_super) && empty($is_operator))
            return abort('403');

        return $next($request);
    }
}
