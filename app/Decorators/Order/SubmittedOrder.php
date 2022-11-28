<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\Order;
use DB;
use Exception;

class SubmittedOrder extends Decorator {

	public function submit() {
		$saved = $this->update([
			'order_status' => Order::STATUS_PENDING_APPROVAL,
		]);

		if (!$saved) {
			throw new Exception("Failed to update order status.");
		}

		return $this;
	}
}