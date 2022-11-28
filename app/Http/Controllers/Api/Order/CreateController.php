<?php namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Order;
use Gate;
use Illuminate\Http\Request;

class CreateController extends Controller {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => "You are not authorized to do that.",
		]);
	}

	public function post(Request $Request) {
		if (Gate::denies('create', Order::class)) {
			return $this->deny();
		}

		$Order = Auth()->User()->Agency->newOrder()->draft()->create();

		return response()->json([
			'success' => true,
			'data' => [
				'Order' => $Order->Original,
			]
		]);
	}
}