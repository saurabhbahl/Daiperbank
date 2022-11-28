<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;

class DiscardedOrder extends Decorator {
	public function discard() {
		$this->delete();

		return $this;
	}
}