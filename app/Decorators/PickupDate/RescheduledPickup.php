<?php namespace App\Decorators\PickupDate;

use App\Decorators\Decorator;
use App\Decorators\Order\RescheduledOrder;
use App\Order;
use App\PickupDate;
use Carbon\Carbon;
use DB;

class RescheduledPickup extends Decorator {

	protected $datetime;
	protected $reason;
	protected $RescheduledPickupDate;

	public function for(Carbon $reschedule_datetime) {
		$this->datetime = $reschedule_datetime;

		return $this;
	}

	public function because($reason) {
		$this->reason = $reason;

		return $this;
	}

	public function save() {
		DB::transaction(function() {
			$this->RescheduledPickupDate = PickupDate::where(DB::raw('DATE(pickup_date)'), $this->datetime->format('Y-m-d'))
									->where('id', '!=', $this->id)
									->first();

			if ( ! $this->RescheduledPickupDate) {
				$this->RescheduledPickupDate = PickupDate::create([
					'pickup_date' => $this->datetime,
				]);
			}

			$this->ApprovedOrder->map(function($Order) {
				$this->rescheduleOrder($Order)->save();
			});

			$this->PendingOrder->map(function($Order) {
				$this->rescheduleOrder($Order)->save();
			});

			// delete this pickup date, after we've rescheduled all the orders
			// this is a soft delete, so any orders that aren't rescheduled will continue
			// to reference this date
			$this->delete();

			$this->migrateFulfillmentsToNewDate();
		});

		return $this->RescheduledPickupDate;
	}

	protected function rescheduleOrder(Order $Order) {
		$RescheduledOrder = $Order->reschedule()
								->for($this->RescheduledPickupDate)
								->because($this->reason);

		return $RescheduledOrder;
	}

	protected function migrateFulfillmentsToNewDate() {
		/**
		 * Migrating "Fulfillments" to the new, rescheduled, pickup date
		 *
		 * Fulfillments are what we refer to when we're talking about batches of orders that are set to be picked.
		 *
		 * When we reschedule an entire pickup date, all the already exported orders for the (now defunkt) date need to be migrated
		 * to the new pickup date. This will allow the user to export the packing assets for the new date, without having to re-export all the orders in it.
		 *
		 * i'm not really sure if this is the best process to follow -- but it's the best i could think of at the time
		 */
		$this->Fulfillment->map(function($Fulfillment) {
			$Fulfillment->update([
				'pickup_date_id' => $this->RescheduledPickupDate->id,
			]);

			$Fulfillment->generatePdfs();
		});
	}
}