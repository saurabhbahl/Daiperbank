<?php namespace App\Decorators\Order;

use App\Decorators\Decorator;
use App\Order;
use App\OrderChild;
use App\PickupDate;
use DB;

class ClonedOrder extends Decorator {
	protected $removed_children;
	protected $NewOrder;

	public function clone() {
		return DB::transaction(function() {
			$this->NewOrder = $this->duplicateOrder();
			$this->addItems();

			return $this->NewOrder;
		});
	}

	protected function duplicateOrder() {
		return Order::create([
			'agency_id' => $this->agency_id,
			'order_type' => $this->order_type,
			'order_status' => Order::STATUS_DRAFT,
			'cloned_from_order_id' => $this->id,
			'created_by_user_id' => Auth()->User()->id,
			'pickup_date_id' => $this->getFirstPickupDate()->id,
		]);
	}

	protected function addItems() {
		$Items = $this->Original->Child->map(function($Child) {
			if (OrderChild::STATE_REJECTED !== $Child->getState()) {
				$OrderChild = $this->NewOrder->addChild($Child->Child);
				// return $OrderChild->addItem($Item->product_id, $Item->quantity);
				return $OrderChild->Item;
			}

			return false;
		})->filter();

		return $Items;
	}

	protected function getFirstPickupDate() {
		$PickupDate = Auth()->User()->Agency->getFirstAvailablePickupDate();

		return $PickupDate ?? new PickupDate;
	}
}