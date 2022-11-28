<?php namespace App;

use App\Notifications\DatabaseNotification;
use App\Notifications\NewNote as NewNoteNotification;
use Illuminate\Database\Eloquent\Model;
use Notification;

class Note extends Model {
	protected $table = 'note';
	public $timestamps = true;
	protected $guarded = [];

	public function Author() {
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function notable() {
		return $this->morphTo('notable');
	}

	public function sendNotification(Agency $CurrentAgency, Order $Order) {
		if ($CurrentAgency->id == $Order->Agency->id) {
			$this->sendNotificationTo(Agency::getAdminAgencies(), $Order);
		} elseif ($this->flag_share) {
			$this->sendNotificationTo($Order->Agency, $Order);
		}

		return $this;
	}

	public function sendNotificationTo($Recipient, Order $Order) {
		$Notification = new NewNoteNotification($Order, $this);

		return Notification::send($Recipient, $Notification);
	}
}
