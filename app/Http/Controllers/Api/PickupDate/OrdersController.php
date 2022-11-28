<?php namespace App\Http\Controllers\Api\PickupDate;

use App\PickupDate;
use Illuminate\Http\Request;
use App\ViewGenerators\PickupDate\OrderList;
use App\Http\Controllers\Controller as BaseController;

class OrdersController extends BaseController {
	public function get(Request $Request, PickupDate $Date) {
		$Orders = $Date->Fulfillment->reduce(function($Orders, $Batch) {
			return $Orders->concat($Batch->Order);
		}, collect());

		$Generator = new OrderList;
		$Orders = $Generator->prepareList($Orders);

		return response()->json([
			'success' => true,
			'data' => $Orders,
		]);
	}
}