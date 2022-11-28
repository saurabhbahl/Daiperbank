<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\Order;

class DraftOrder extends Decorator {
	static public function make($instance) {
		$instance = parent::make($instance);
		$instance->order_status = Order::STATUS_DRAFT;

		return $instance;
	}

	public function create() {
		$this->save();

		return $this;
	}
}