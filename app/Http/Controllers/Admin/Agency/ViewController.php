<?php namespace App\Http\Controllers\Admin\Agency;

use App\Agency;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class ViewController extends BaseController {

	public function get(Request $Request, Agency $Agency) {
		return view('admin.agency.view', [
			'Agency' => $Agency,
			'editing' => $Request->exists('edit'),
		]);
	}

}