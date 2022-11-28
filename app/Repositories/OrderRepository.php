<?php namespace App\Repositories;

use App\Inventory;
use App\InventoryAdjustment;
use App\Order;
use DB;
use Illuminate\Support\Collection;
use App\Queries\OrderSummaryQuery;

class OrderRepository {

	public function getOrders() {
		return Order::with(['PickupDate', 'Agency', 'Child', 'Child.Location', 'Item', 'Item.Product', 'Item.Product.Category'])
						->orderBy('created_at', 'DESC')
						->orderBy('id', 'DESC')
						->get();
	}

	public function find($order_id) {
		return Order::with(['PickupDate', 'Agency', 'Child', 'Child.Location', 'Item', 'Item.Product'])
					->find($order_id);
	}

	public function findNextPreviousOrders(Order $Order) {
		return new class($Order) {
			public $Previous;
			public $Next;

			public function __construct(Order $Origin) {
				$this->Previous = Order::where(function ($Query) use ($Origin) {
					$Query->where('created_at', '<', $Origin->created_at)
							->orWhere('id', '<', $Origin->id);
				})
				->orderBy('created_at', 'DESC')
				->orderBy('id', 'DESC')
				->take(1)
				->first();

				$this->Next = Order::where(function ($Query) use ($Origin) {
					$Query->where('created_at', '>', $Origin->created_at)
							->orWhere('id', '>', $Origin->id);
				})
				->orderBy('created_at', 'ASC')
				->orderBy('id', 'ASC')
				->take(1)
				->first();
			}
		};
	}

	public function approve($order_id) {
		$Order = $this->find($order_id);

		if (!$Order) {
			abort(404);
		}

		$Order->update([
			'order_status' => Order::STATUS_PENDING_PICKUP,
		]);

		InventoryAdjustment::createFromOrder($Order);

		return true;
	}

	public function getStatusSummary() {
		return OrderStatusSummaryQuery::create()->getSummary();
	}
}
