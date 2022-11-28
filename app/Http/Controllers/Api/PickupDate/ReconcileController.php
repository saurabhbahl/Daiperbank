<?php namespace App\Http\Controllers\Api\PickupDate;

use Exception;
use App\PickupDate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Decorators\PickupDate\ReconciledPickup;
use App\Http\Controllers\Controller as BaseController;
use App\ViewGenerators\PickupDate\ExportedFulfillments;

class ReconcileController extends BaseController {
	public function post(Request $Request, PickupDate $PickupDate) {
		try {
			$this->validate($Request, $this->rules());

			$PickupDate->reconcile($Request->get('orders'));
			$PickupDate->fresh()->load(
				'Fulfillment',
				'Fulfillment.Order.Agency',
				'Fulfillment.Order.ApprovedItem',
				'Fulfillment.Order.ApprovedItem.Product',
				'Fulfillment.Order.ApprovedChild'
			);

			$ExportedFulfillments = new ExportedFulfillments;
			$PickupDates = $ExportedFulfillments->prepareList(collect([$PickupDate]));

			return response()->json([
				'success' => true,
				'data' => [
					'PickupDate' => $PickupDates->first(),
				],
			]);
		}
		catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => "Invalid data submitted",
				'data' => [
					'errors' => $e->validator->errors(),
				],
			], 422);
		}
		catch (Exception $e) {
			throw $e;
			return response()->json([
				'success' => false,
				'message' => 'An unexpected error occurred.',
			], 500);
		}
	}

	protected function rules() {
		return [
			'orders' => ['array'],
		];
	}
}