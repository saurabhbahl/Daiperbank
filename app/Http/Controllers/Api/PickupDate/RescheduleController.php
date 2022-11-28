<?php namespace App\Http\Controllers\Api\PickupDate;

use App\Decorators\PickupDate\RescheduledPickup;
use App\Http\Controllers\Controller as BaseController;
use App\PickupDate;
use Illuminate\Http\Request;

class RescheduleController extends BaseController {

	public function post(Request $Request, PickupDate $Date) {
		$this->validate($Request, $this->rules());

		$RescheduledPickup = $Date->reschedule()
								->for(carbon($Request->get('reschedule_for')))
								->because($Request->get('reschedule_reason'));

		if ($PickupDate = $RescheduledPickup->save()) {

			$PickupDate->refresh();
			$PickupDate->with('Order');

			return response()->json([
				'success' => true,
				'data' => [
					'PickupDate' => $PickupDate,
				],
			]);
		}

		$PickupDate->refresh();
		$PickupDate->load('Order');

		return response()->json([
			'success' => false,
			'message' => 'Could not reschedule order pickups on ' . $Date->pickup_date->format('m-d-Y'),
		], 500);
	}

	public function rules() {
		return [
			'reschedule_for' => ['required', 'date_format:Y-m-d\TH:i'],
			'reschedule_reason' => ['string'],
		];
	}
}