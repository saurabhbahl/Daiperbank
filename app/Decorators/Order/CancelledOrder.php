<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\InventoryAdjustment;
use App\Note;
use App\Notifications\OrderCancelled;
use App\Order;
use DB;

class CancelledOrder extends Decorator {
	protected $reason;
	protected $silent;

	public static function create(Order $Order) {
		return new static($Order);
	}

	public function because($reason) {
		$this->reason = $reason;

		return $this;
	}

	public function reason($reason) {
		return $this->because($reason);
	}

	public function silent($silent = true) {
		$this->silent = $silent;

		return $this;
	}

	public function cancel() {
		DB::transaction(function() {
			$this->updateStatus();

			if ($this->reason) {
				$this->saveReasonAsNote();
			}

			$this->adjustInventory();
			$this->sendNotification();

			return true;
		});

		return $this;
	}

	protected function updateStatus() {
		$this->Original->update([
			'order_status' => Order::STATUS_CANCELLED,
		]);
	}

	protected function saveReasonAsNote() {
		$note = <<<EONOTE
**Automaticaly Generated Note***

Order Cancelled

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

	protected function adjustInventory() {
		$Adjustment = InventoryAdjustment::where('order_id', $this->id)->first();

		if ($Adjustment) {
			$Adjustment->delete();
		}

		return $this;
	}

	public function sendNotification() {
		if ( ! $this->silent) {
			$this->Agency->notify( (new OrderCancelled($this->Original))->withReason($this->reason) );
		}

		return $this;
	}
}