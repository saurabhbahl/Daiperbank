<?php

namespace App\Http\Controllers\Auth;

use App\Decorators\User\MasqueradingUser;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller {
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
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct() {
		$this->middleware('guest')->except('logout');
	}

	public function username() {
		return 'username';
	}

	public function logout(Request $Request) {
		if (Auth()->User() && Auth()->User()->isBeingImpersonated()) {
			$CurrentUser = Auth()->User();
			$CurrentUser->stopImpersonating();

			flash("You've been logged out of the " . $CurrentUser->Agency->name . " account.")->success();

			return redirect()->route('admin.agency.index');
		}

		$this->guard()->logout();
		$Request->session()->flush();
		$Request->session()->regenerate();

		return redirect('/login');
	}

	 protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['flag_active' => 1]);
    }
}
