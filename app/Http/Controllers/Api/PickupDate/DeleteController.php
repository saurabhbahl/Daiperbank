<?php namespace App\Http\Controllers\Api\PickupDate;

use App\Http\Controllers\Controller as BaseController;
use App\PickupDate;
use Illuminate\Http\Request;

class DeleteController extends BaseController {
	public function delete(Request $Request, PickupDate $Date) {
		$Date->delete();

		return response()->json([
			'success' => true,
		]);
	}
}