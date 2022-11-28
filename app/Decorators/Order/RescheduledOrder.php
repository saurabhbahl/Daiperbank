<?php namespace App\Decorators\Order;

use DB;
use App\Order;
use App\PickupDate;
use App\InventoryAdjustment;
use App\Decorators\Decorator;
use App\Notifications\OrderRescheduled as OrderRescheduledNotification;

class RescheduledOrder extends Decorator {

	protected $NewPickupDate;
	protected $OldPickupDate;
	protected $reason;
	protected $silent;

	protected $ClonedOrder;
	protected $CancelledOrder;

	public function for(PickupDate $PickupDate) {
		$this->NewPickupDate = $PickupDate;

		return $this;
	}

	public function because($reason) {
		$this->reason = $reason;

		return $this;
	}

	public function silent($silent = true) {
		$this->silent = $silent;

		return $this;
	}

	public function save() {
		$success = DB::transaction(function() {
			$NewPickupDate = $this->NewPickupDate;
			$this->OldPickupDate = $OldPickupDate = $this->PickupDate;

			$this->update([
				'pickup_date_id' => $this->NewPickupDate->id,
			]);

			$Adjustment = InventoryAdjustment::adjustmentForOrderExists($this->Original);

			if ($Adjustment) {
				$Adjustment->update([
					'adjustment_datetime' => $NewPickupDate->pickup_date,
				]);
			}

			$this->refresh();

			$note = <<<EONOTE
**Automaticaly Generated Note***

Order Pickup Rescheduled

Previous Pickup Date: {$OldPickupDate->pickup_date->format('m-d-Y @ g:ia')}
New Pickup Date: {$NewPickupDate->pickup_date->format('m-d-Y @ g:i:a')}

Reason:
{$this->reason}
EONOTE;

			$this->Note()->create([
				'note' => $note,
				'flag_share' => 1,
				'user_id' => auth()->user()->id,
			]);

			$this->sendNotification();
		});

		return $this->Original;
	}

	protected function sendNotification() {
		if ( ! $this->silent) {
			$this->Agency->notify(new OrderRescheduledNotification($this->Original, $this->OldPickupDate, $this->reason));
		}
	}
}