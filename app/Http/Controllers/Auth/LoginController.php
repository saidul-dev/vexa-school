<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);


        if(school_status_check($input['email']) == 1 || user_role_check($input['email']) == 1 || user_role_check($input['email']) == 2) {

            if (auth()->attempt(array('email' => $input['email'], 'password' => $input['password']))) {
                if (auth()->user()->role_id == 1) {

                    session(['superadmin_login' => 1]);
                    return redirect()->route('superadmin.dashboard');

                } else {
                    if (auth()->user()->role_id == 2) {

                        session(['admin_login' => 2]);
                        return redirect()->route('admin.dashboard');

                    } else if (auth()->user()->role_id == 3) {

                        session(['teacher_login' => 3]);
                        return redirect()->route('teacher.dashboard');

                    } else if (auth()->user()->role_id == 4) {

                        session(['accountant_login' => 4]);
                        return redirect()->route('accountant.dashboard');

                    } elseif ((auth()->user()->role_id == 5)) {

                        session(['librarian_login' => 5]);
                        return redirect()->route('librarian.dashboard');

                    } elseif ((auth()->user()->role_id == 6)) {

                        session(['parent_login' => 6]);
                        return redirect()->route('parent.dashboard');

                    } else if (auth()->user()->role_id == 7) {

                        session(['student_login' => 7]);
                        return redirect()->route('student.dashboard');

                    }  else if (auth()->user()->role_id == 8) {

                        session(['driver_login' => 8]);
                        return redirect()->route('driver.dashboard');
                        
                    }   else if (auth()->user()->role_id == 9) {

                        session(['alumni_login' => 9]);
                        return redirect()->route('alumni.dashboard');

                    }else {
                        return redirect()->route('home');
                    }
                }
            } else {
                return redirect()->route('login')
                    ->with('error', 'Email-Address And Password Are Wrong.');
            }
        } else {
            return redirect()->route('login')
                    ->with('error', 'Your school is yet to be authorized to this service!');
        }
    }
}
