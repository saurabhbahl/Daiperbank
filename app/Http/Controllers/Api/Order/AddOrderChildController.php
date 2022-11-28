<?php namespace App\Http\Controllers\Api\Order;

use App\Child;
use App\Http\Controllers\Controller as BaseController;
use App\Order;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AddOrderChildController extends BaseController {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => 'You are not authorized to do that.',
		]);
	}

	public function post(Request $Request, Order $Order) {

		if (Gate::denies('update', $Order)) {
			return $this->deny();
		}

		try {
			$this->validate($Request, $this->rules(), $this->messages());
			$Order->addChild( $Child = Child::find($Request->get('child_id')) );

			$Order->refresh();
			$Order->load('Agency',
				'Child',
				'Child.Child',
				'Child.Child.Guardian',
				'Child.Item',
				'Child.Item.Product',
				'Child.Item.Product.Category');

			return response()->json([
				'success' => true,
				'data' => [
					'Order' => $Order,
					'Child' => $Child,
				],
			]);
		}
		catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => 'Could not add child to the requested order.',
				'data' => [
					'errors' => $e->validator->errors(),
				],
			], 422);
		}
		catch (Exception $e) {
			// TODO Log the exception that happened
		}

		return response()->json([
			'success' => false,
			'message' => 'An unexpected error occurred.',
		], 500);
	}

	protected function rules() {
		return [
			'child_id' => ['required', 'numeric', 'exists:child,id', ],
			// TODO -- validate that the current user is authorized to add this child to the order.
		];
	}

	protected function messages() {
		return [
			'child_id.required' => "You must select a child to add.",
			"child_id.numeric" => "That is not a valid child.",
			"child_id.exists" => "That is not a valid child.",
		];
	}
}