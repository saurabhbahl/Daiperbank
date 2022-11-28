<?php namespace App\Queries;

use DB;
use App\Order;

class OrderProductSummaryQuery extends Query {
	protected $order_ids = [];

	public function get() {
		return $this->buildQuery()->get();
	}

	public function orderIds(array $ids) {
		$this->order_ids = $ids;

		return $this;
	}

	public function getSummary() {
		$Products = ProductInventoryQuery::create()->get();

		$this->get()->map(function($ordered_product) use ($Products) {
			$Product = $Products->get($ordered_product->product_id);

			if ($Product->Inventory) {
				$Product->Inventory->pending_approval = $ordered_product->pending_approval;
				$Product->Inventory->pending_pickup = $ordered_product->pending_pickup;
				$Product->Inventory->ordered = $ordered_product->ordered;
			}
		});

		return $Products;
	}

	public function buildQuery() {
		$this->Query = DB::table('order')
			->select([
				DB::raw('product_id'),
				DB::raw('SUM(
					CASE
						WHEN `order`.order_status = \'' . Order::STATUS_PENDING_APPROVAL . '\' THEN order_item.quantity
						ELSE 0
					END
				) pending_approval'),
				DB::raw('SUM(
					CASE
						WHEN `order`.order_status = \'' . Order::STATUS_PENDING_PICKUP . '\' THEN order_item.quantity
						ELSE 0
					END
				) pending_pickup'),
				DB::raw('SUM(order_item.quantity) ordered'),
			])
			->join('order_item', 'order.id', '=', 'order_item.order_id')
			->join('product', 'product.id', '=', 'order_item.product_id')
			->groupBy('product_id')
			->orderBy('product.product_category_id', 'asc')
			->orderBy('product.id', 'asc');

		if ($this->order_ids) {
			$this->Query->whereIn('order.id', $this->order_ids);
		}

		return $this->Query;
	}
}