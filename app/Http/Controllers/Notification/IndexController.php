<?php namespace App\Http\Controllers\Notification;

use App\Agency;
use App\Http\Controllers\Controller as BaseController;
use App\Notifications\BroadcastNotification;
use Illuminate\Http\Request;
use Notification;

class IndexController extends BaseController {
	public function get() {
		return view('notifications.index', [
			'AllNotifications' => Auth()->User()->Agency->notifications,
		]);
	}

	public function post(Request $Request) {
		try {
			$this->validate($Request, $this->rules());

			Notification::send(Agency::getAllPartners(), new BroadcastNotification($Request->get('message')));

			flash('Your notification is being delivered to all agency partners.')->success();
			return redirect()->route('notifications.index');

		} catch (ValidationException $e) {
			flash("Could not send your notification. Please check the message and try again.")->error();
		} catch (Exception $e) {
			flash("An unexpected error occurred while sending your message. Please try again.")->error();
		}

		return redirect()->route('notifications.index')->withInput();
	}

	protected function rules() {
		return [
			'message' => ['required', 'string', 'min:5'],
		];
	}
}