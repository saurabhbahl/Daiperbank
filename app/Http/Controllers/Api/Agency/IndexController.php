<?php namespace App\Http\Controllers\Api\Agency;

use App\Agency;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Support\Arr;

class IndexController extends BaseController {
	public function get() {
		$Agencies = Agency::all();

		return response()->json([
			'success' => ! ! $Agencies,
			'data' => $Agencies->map(function ($Agency) {
				return Arr::only($Agency->toArray(), ['id', 'id_prefix', 'name', 'address', 'city', 'state', 'zip']);
			}),
		]);
	}
}
