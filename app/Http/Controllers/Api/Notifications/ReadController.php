<?php namespace App\Http\Controllers\Api\Notifications;

use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReadController extends BaseController {
	public function post(Request $Request) {
		try {
			$this->validate($Request, $this->rules());

			Auth()->User()->Agency->markNotificationsRead($Request->get('since'), $Request->get('until'));

			return response()->json([
				'success' => true,
			]);
		} catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => 'Invalid data posted',
				'data' => [
					'errors' => $e->validator->errors(),
				],
			], 400);
		} catch (Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'An unexpected error occurred.',
			], 500);
		}
	}

	protected function rules() {
		return [
			'since', ['numeric', 'date_format:U', 'max:' . time(), ],
			'until', ['numeric', 'date_format:U', 'max:' . time(), ],
		];
	}
}