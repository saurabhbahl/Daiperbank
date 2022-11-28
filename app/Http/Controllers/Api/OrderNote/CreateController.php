<?php namespace App\Http\Controllers\Api\OrderNote;

use App\Http\Controllers\Controller as BaseController;
use App\Order;
use Illuminate\Http\Request;
use Validator;

class CreateController extends BaseController {
	public function post(Request $Request, Order $Order) {
		$Validator = Validator::make($Request->all(), $this->rules(), $this->messages());

		if ($Validator->fails()) {
			abort(422);
		}

		$Note = $Order->Note()->create([
			'note' => $Request->get('note'),
			'flag_shared' => $Request->get('flag_shared'),
			'user_id' => auth()->user()->id,
		]);

		if ($Note) {
			$Note = $Note->with(['Author'])->find($Note->id);

			return response()->json([
				'success' => true,
				'Note' => $Note->toArray(),
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'Couldn\'t create order note.',
		], 500);
	}

	protected function rules() {
		return [
			'note' => ['required', 'string', 'min:2',],
			'flag_shared' => ['bool'],
		];
	}

	protected function messages() {
		return [
		];
	}
}