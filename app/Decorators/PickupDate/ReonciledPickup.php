<?php namespace App\Decorators\PickupDate;

use App\Decorators\Decorator;
use DB;

class ReconciledPickup extends Decorator {
	protected $fulfilled_orders;

	public function fulfillOrders($order_ids) {
		$this->fulfilled_orders = $order_ids;

		return $this;
	}

	public function save() {
		return DB::transaction(function() {
			$this->update([
				'reconciled_at' => carbon(),
			]);

			$this->Fulfillment->map(function($Batch) {
				$Batch->Order->map(function($Order) {
					if (in_array($Order->id, $this->fulfilled_orders)) {
						$Order->fulfill();
					} else {
						$Order->cancel()->reason("Order not reconciled on pickup date.")->cancel();
					}
				});
			});

			return true;
		});
	}
}