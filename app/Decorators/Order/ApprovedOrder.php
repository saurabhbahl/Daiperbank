<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\InventoryAdjustment;
use App\Notifications\OrderApproved as OrderApprovedNotification;
use App\Order;
use DB;
use Exception;

class ApprovedOrder extends Decorator {

	protected $silent = false;

	public static function create(Order $Order) {
		return new static($Order);
	}

	public function silent($silent = true) {
		$this->silent = $silent;

		return $this;
	}

	public function isSilent() {
		return $this->silent;
	}

	public function approve() {

		if ( ! $this->PendingChildren->count() && ! $this->ApprovedChildren->count()) {
			throw new Exception("Can not approve order with no eligible items/children");
		}

		DB::transaction(function() {
			$this->updateStatus();
			$this->approvePendingChildren();
			$this->updateInventory();
			$this->sendNotification();

			return true;
		});

		return $this;
	}

	protected function updateStatus() {
		$saved = $this->update([
			'order_status' => Order::STATUS_PENDING_PICKUP,
		]);

		if (!$saved) {
			throw new Exception("Failed to update order status.");
		}

		return $this;
	}

	protected function approvePendingChildren() {
		$pending_count = $this->PendingChildren->count();

		if ( ! $pending_count) {
			return $this;
		}

		$updated = $this->PendingChildren->map(function($Child) {
			return $Child->approve();
		})->filter();

		if ($updated->count() != $pending_count) {
			// not all the pending children got updated...this is a problem
			throw new Exception("Failed to approve all pending children on order.");
		}

		return $this;
	}

	protected function updateInventory() {
		InventoryAdjustment::createFromOrder($this->Original);

		return $this;
	}

	protected function sendNotification() {
		if ( ! $this->silent) {
			$this->Agency->notify(new OrderApprovedNotification($this->Original));
		}

		return $this;
	}
}