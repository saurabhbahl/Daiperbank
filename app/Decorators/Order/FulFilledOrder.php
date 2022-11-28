<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\Order;

class FulfilledOrder extends Decorator {
	public function save() {
		$this->update([
			'order_status' => Order::STATUS_FULFILLED,
		]);
	}
}