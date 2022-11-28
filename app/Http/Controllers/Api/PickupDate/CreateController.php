<?php namespace App\Http\Controllers\Api\PickupDate;

use App\Http\Controllers\Controller as BaseController;
use App\PickupDate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreateController extends BaseController {
	public function post(Request $Request) {
		try {
			$this->validate($Request, $this->rules());

			$PickupDate = PickupDate::create([
				'pickup_date' => $Request->get('pickup_date'),
			]);

			return response()->json([
				'success' => true,
				'data' => [
					'PickupDate' => $PickupDate,
				],
			]);
		}
		catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => "Invalid data",
				'data' => [
					'errors' => $e->validator->errors(),
				],
			]);
		}
		catch (Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Unknown error',
			]);
		}
	}

	public function rules() {
		return [
			'pickup_date' => ['required', 'date_format:Y-m-d H:i:s'],
		];
	}
}