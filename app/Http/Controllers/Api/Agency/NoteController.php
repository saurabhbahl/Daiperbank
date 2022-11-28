<?php namespace App\Http\Controllers\Api\Agency;

use App\Agency;
use App\Http\Controllers\Controller as BaseController;
use App\Note;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Gate;

class NoteController extends BaseController {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => 'You are not authorized to do that.',
		]);
	}

	public function post(Request $Request, Agency $Agency) {
		if (Gate::denies('createNote', $Agency)) {
			return $this->deny();
		}

		try {
			$this->validate($Request, $this->rules(), $this->messages());

			$Note = $Agency->Note()->create([
				'user_id' => Auth()->User()->id,
				'note' => $Request->get('note'),
			]);

			$Agency->refresh();

			return response()->json([
				'success' => true,
				'data' => [
					'Note' => $Note,
					'Notes' => $Agency->Note,
				],
			]);
		} catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => "Could not save note. Please check below for errors.",
				'data' => [
					'errors' => $e->validator->errors(),
				],
			], 422);
		} catch (Exception $e) {
			return responsee()->json([
				'success' => false,
				'message' => 'An unexpected error has occurred.',
			], 500);
		}
	}

	protected function rules() {
		return [
			'note' => ['required', 'string', 'min:5'],
		];
	}

	protected function messages() {
		return [
			'note.required' => "You must type a note...",
			'note.min' => "Your note is too short.",
		];
	}
}