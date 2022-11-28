<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\Order;

class PendingOrder extends Decorator {
	public function save() {
		$this->update([
			'order_status' => Order::STATUS_PENDING_APPROVAL,
		]);

		return $this;
	}
}
