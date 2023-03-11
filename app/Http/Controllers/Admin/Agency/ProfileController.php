<?php namespace App\Http\Controllers\Admin\Agency;

use App\Agency;
use App\AgencySearch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProfileController extends Controller
{
    public function get($id) {
        $agency = $id;
        // dd($agency);
		return view('admin.agency.profile', [
			'agency' => $agency
		]);
	}
}