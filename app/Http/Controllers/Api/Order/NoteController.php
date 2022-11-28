<?php namespace App\Http\Controllers\Api\Order;

use App\Agency;
use App\Http\Controllers\Controller as BaseController;
use App\Note;
use App\Notifications\NewNote as NewNoteNotification;
use App\Order;
use Exception;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Notification;

class NoteController extends BaseController {
	public function deny() {
		return response()->json([
			'success' => false,
			'message' => 'You are not authorized to do that.',
		]);
	}

	public function post(Request $Request, Order $Order) {
		if (Gate::denies('note', $Order)) {
			return $this->deny();
		}

		try {
			$this->validate($Request, $this->rules(), $this->messages());

			$Note = $Order->Note()->create([
				'user_id' => Auth()->User()->id,
				'note' => $Request->get('note'),
				'flag_share' => $Request->get('flag_share', false),
			]);

			$Note->sendNotification(Auth()->User()->Agency, $Order);

			return response()->json([
				'success' => true,
				'data' => [
					'Note' => $Note->load('Author'),
					'Notes' => $Order->load('Note', 'Note.Author')->Note,
				],
			]);
		}
		catch (ValidationException $e) {
			return response()->json([
				'success' => false,
				'message' => 'Could not create this note',
				'data' => [
					'errors' => $e->validator->errors(),
				],
			], 422);
		}
		catch (Exception $e) {
			throw $e;
			return response()->json([
				'success' => false,
				'message' => 'Could not create note. Unknown error occurred',
				'data' => [
					'exception' => [
						'message' => $e->getMessage(),
						'code' => $e->getCode(),
					],
				],
			]);
		}
	}

	public function delete(Request $Request, Order $Order, Note $Note) {
		if (Gate::denies('note', $Order)) {
			return $this->deny();
		}

		if (Gate::denies('delete', $Note)) {
			return $this->deny();
		}

		try {
			$Note->delete();

			return response()->json([
				'success' => true,
				'data' => [
					'Notes' => $Order->load('Note', 'Note.Author')->Note,
				],
			]);
		}
		catch (Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Could not delete note. Unknown error occurred',
				'data' => [
					'exception' => [
						'message' => $e->getMessage(),
						'code' => $e->getCode(),
					],

					'Note' => $Note,
				],
			]);
		}
	}

	protected function rules() {
		return [
			'note' => ['required', 'string', 'min:5'],
			'flag_share' => ['boolean', 'nullable'],
		];
	}

	protected function messages() {
		return [
			'note.required' => 'This field is required.',
			'note.string' => 'This field is required.',
			'note.min' => 'Your comment isn\'t long enough.',
		];
	}
}