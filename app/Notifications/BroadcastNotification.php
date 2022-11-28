<?php namespace App\Notifications;

use App\Mail\BroadcastNotification as BroadcastNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Database\Eloquent\Model;

class BroadcastNotification extends Notification {
	use Queueable;

	protected $message;

	public function __construct($message) {
		$this->message = $message;
	}

	public function via($notifiable) {
		return ['mail', 'database'];
	}

	public function toMail($notifiable) {
		$Mailable = (new BroadcastNotificationMail($notifiable, $this->message))
						->subject("New message from Healthy Steps Diaper Bank")
						->to($notifiable->routeNotificationFor('mail'));

		return $Mailable;
	}

	public function toArray($notifiable) {
		return [
			'message' => $this->message,
		];
	}

	static public function getView() {
		return 'notifications.broadcast_notification';
	}

	static public function getViewData($Notification, $data) {
		return [
			'message' => $data['message'],
		];
	}
}