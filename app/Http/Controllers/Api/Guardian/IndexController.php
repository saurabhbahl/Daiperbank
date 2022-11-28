<?php namespace App\Http\Controllers\Api\Guardian;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class IndexController extends BaseController {
	public function get(Request $Request) {
		return response()->json([
			'success' => true,
			'data' => [
				'Guardians' => Auth()->User()->Agency->Guardian()->orderBy('name', 'ASC')->get(),
			],
		]);
	}
}