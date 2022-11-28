<?php namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller as BaseController;
use App\Order;
use App\OrderChild;
use Exception;
use Gate;
use Illuminate\Http\Request;

class OrderChildApprovalController extends BaseController {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => 'You are not authorized to do that.',
		]);
	}

	public function post(Request $Request, Order $Order, OrderChild $OrderChild) {
		if (Gate::denies('approve', $Order)) {
			return $this->deny();
		}

		try {
			$OrderChild->approve();
			$OrderChild->refresh();
			$OrderChild->load('Child', 'Child.Guardian', 'Item', 'Item.Product', 'Item.Product.Category');

			return response()->json([
				'success' => true,
				'data' => [
					'Child' => $OrderChild,
				]
			]);
		}
		catch (Exception $e) {
			return response()->json([
				'success' => false,
			]);
		}
	}

	public function delete(Request $Request, Order $Order, OrderChild $OrderChild) {
		if (Gate::denies('approve', $Order)) {
			return $this->deny();
		}

		try {
			$OrderChild->restoreToPending();
			$OrderChild->refresh();
			$OrderChild->load('Child', 'Child.Guardian', 'Item', 'Item.Product', 'Item.Product.Category');

			return response()->json([
				'success' => true,
				'data' => [
					'Child' => $OrderChild,
				],
			]);
		}
		catch (Exception $e) {
			throw $e;
			return response()->json([
				'success' => false,
			]);
		}
	}
}