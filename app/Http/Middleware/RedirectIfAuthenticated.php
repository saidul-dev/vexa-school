<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if (auth()->user()->role_id =='1') {
                    return redirect('/superadmin/dashboard');
                }elseif(auth()->user()->role_id =='2'){
                    return redirect('/admin/dashboard');
                }elseif(auth()->user()->role_id =='3'){
                    return redirect('/teacher/dashboard');
                }elseif(auth()->user()->role_id =='4'){
                    return redirect('/accountant/dashboard');
                }elseif(auth()->user()->role_id =='5'){
                    return redirect('/librarian/dashboard');
                }elseif(auth()->user()->role_id =='6'){
                    return redirect('/parent/dashboard');
                }elseif(auth()->user()->role_id =='7'){
                    return redirect('/student/dashboard');
                }elseif(auth()->user()->role_id =='9'){
                    return redirect('/alumni/dashboard');
                }
            }

        }

        return $next($request);
    }
}
