<?php namespace App\Notifications;

use App\Mail\NewNote as NewNoteMail;
use App\Note;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Database\Eloquent\Model;

class NewNote extends Notification {
	use Queueable;

	protected $Model;
	protected $Note;

	public function __construct(Model $Model, Note $Note) {
		$this->Model = $Model;
		$this->Note = $Note;
	}

	public function via($notifiable) {
		return ['mail', 'database'];
	}

	public function toMail($notifiable) {
		$Mailable = (new NewNoteMail($notifiable, $this->Model, $this->Note))
						->subject("New Comment on Order #{$this->Model->full_id}")
						->to($notifiable->routeNotificationFor('mail'));

		return $Mailable;
	}

	public function toArray($notifiable) {
		return [
			'note_id' => $this->Note->id,
			'order_id' => $this->Model->id,
			'agency_id' => $notifiable->id,
		];
	}

	static public function getView() {
		return 'notifications.note_created';
	}

	static public function getViewData($Notification, $data) {

		$note = Note::with('Author', 'Author.Agency')->find($data['note_id']);

		return [
			'Note' => $note,
			'Order' => Order::find($data['order_id']),
		];
	}
}