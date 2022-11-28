<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\Note;
use App\Notifications\OrderRejected as OrderRejectedNotification;
use App\Order;
use DB;

class RejectedOrder extends Decorator {

	protected $reason = null;
	protected $flag_share_reason = false;

	protected $Reason;

	public static function create(Order $Order) {
		return new static($Order);
	}

	public function reason(string $reason = null) {
		$this->reason = $reason;

		return $this;
	}

	public function notifyCreator(bool $flag = false) {
		$this->flag_share_reason = $flag;

		return $this;
	}

	public function reject() {
		DB::transaction(function() {
			$this->updateStatus();

			if ($this->reason) {
				$this->saveReasonAsNote();
			}

			$this->sendNotification();
		});

		return $this;
	}

	public function updateStatus() {
		$saved = $this->update([
			'order_status' => Order::STATUS_REJECTED,
		]);

		if (!$saved) {
			throw new Exception("Failed to update order status.");
		}

		return $this;
	}

	public function saveReasonAsNote() {
				$note = <<<EONOTE
**Automaticaly Generated Note***

Order Rejected

Reason:
{$this->reason}
EONOTE;
		Note::create([
			'model' => Order::class,
			'model_id' => $this->id,
			'user_id' => Auth()->User()->id,
			'note' => $note,
			'flag_share' => true,
		]);

		return $this;
	}

	public function sendNotification() {
		$this->Agency->notify( (new OrderRejectedNotification($this->Original))->withReason($this->reason) );
		return $this;
	}
}