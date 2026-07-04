<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ParentMiddleware
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
        $user = auth()->user();
       
        if ($user->role_id == '6' && $user->account_status != 'disable') {
            return $next($request);
        }else{
            return redirect()->route('parent.account_disable')->with('error', 'Access denied or your account is disabled.');
        }
    }
}
