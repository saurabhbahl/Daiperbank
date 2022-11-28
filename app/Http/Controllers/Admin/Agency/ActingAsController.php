<?php namespace App\Http\Controllers\Admin\Agency;

use App\Agency;
use App\Decorators\User\MasqueradingUser;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class ActingAsController extends BaseController {
	public function post(Request $Request, Agency $Agency) {
		Auth()->User()->impersonate($Agency->User()->first());

		flash("Successfully logged in as {$Agency->name}.")->success();

		return redirect()->route('home');
	}
}