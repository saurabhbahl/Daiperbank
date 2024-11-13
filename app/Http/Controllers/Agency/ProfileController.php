<?php namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Agency;


class ProfileController extends Controller
{
    public function get() {
		// var_dump($id);
        //var_dump(Auth::id()); exit;
        $id = Auth::id();
        $agency=Agency::findOrFail($id);
        //dd($agency);
		return view('agency.profile', [
			'agency' => $agency
		]);
	}
}