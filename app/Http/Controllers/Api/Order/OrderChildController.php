<?php namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller as BaseController;
use App\Note;
use App\Order;
use App\OrderChild;
use Exception;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderChildController extends BaseController {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => 'You are not authorized to do that.',
		]);
	}

	public function post(Request $Request, Order $Order, OrderChild $OrderChild) {

		if (Gate::denies('update', $Order)) {
			return $this->deny();
		}

		try {
			$this->validate($Request, $this->rules(), $this->messages());

			DB::transaction(function() use ($Request, $OrderChild) {
				$OrderChild->update($Request->only('weight', 'status_potty_train'));
				$OrderChild->updateChildData();
				[ 'product_id' => $product_id, 'quantity' => $quantity ] = $Request->only('product_id', 'quantity');
				$OrderChild->addItem($product_id, $quantity);

				if ($Request->get('note')) {
					Note::create([
						'user_id' => Auth()->User()->id,
						'model' => OrderChild::class,
						'model_id' => $OrderChild->id,
						'note' => $Request->get('note'),
						'flag_share' => $Request->get('flag_note_share', false),
					]);
				}
			});

			$OrderChild->refresh();
			$OrderChild->load('Child', 'Child.Guardian', 'Item', 'Item.Product', 'Item.Product.Category');

			return response()->json([
				'success' => true,
				'data' => [
					'Child' => $OrderChild,
				]
			]);
		}
		catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => "Could not update child. Ensure all required info is filled in.",
				'data' => [
					'errors' => $e->validator->errors(),
				],
			]);
		}
		catch (Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An unexpected error occurred.',
				'data' => [
					'exception' => [
						'message' => $e->getMessage(),
						'code' => $e->getCode(),
					]]
			]);
		}
	}

	public function delete(Request $Request, Order $Order, OrderChild $Child) {
		if (Gate::denies('update', $Order)) {
			return $this->deny();
		}

		try {
			$Child->removeFromOrder($Request->get('force', false));
			$Child->refresh();
			$Child->load('Child', 'Child.Guardian', 'Item', 'Item.Product', 'Item.Product.Category');

			return response()->json([
				'success' => true,
				'data' => [
					'Child' => $Child,
				],
			]);
		}
		catch (Exception $e) {
			return response()->json([
				'success' => true,
				'message' => 'Unknown error',
				'data' => [
					'exception' => [
						'method' => $e->getMessage(),
						'code' => $e->getCode(),
					],
				]
			]);
		}
	}

	public function restore(Request $Request, Order $Order, OrderChild $Child) {
		if (Gate::denies('update', $Order)) {
			return $this->deny();
		}

		try {
			$Child->restoreToOrder();
			$Child->refresh();
			$Child->load('Child', 'Child.Guardian', 'Item', 'Item.Product', 'Item.Product.Category');

			return response()->json([
				'success' => true,
				'data' => [
					'Child' => $Child,
				],
			]);
		}
		catch (Exception $e) {
			return response()->json([
				'success' => true,
				'message' => 'Unknown error',
				'data' => [
					'exception' => [
						'method' => $e->getMessage(),
						'code' => $e->getCode(),
					],
				]
			]);
		}
	}

	protected function rules() {
		return [
			'product_id' => ['required', 'integer', 'exists:product,id', ],
			'quantity' => ['required', 'integer', 'min:1', ],
			'note' => ['string', 'nullable', 'min:1'],
			'flag_note_share' => ['boolean', 'nullable'],
		];
	}

	protected function messages() {
		return [
			'product_id.required' => "You must select a diaper type and size.",
			'product_id.integer' => "Invalid diaper type and size selected.",
			'product_id.exists' => "Invalid diaper type and size selected.",
			'quantity.required' => "This field is required.",
			'quantity.integer' => "This field is required.",
			'quantity.min' => "This field is required.",
		];
	}
}