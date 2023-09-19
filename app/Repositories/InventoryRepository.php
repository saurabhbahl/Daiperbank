<?php namespace App\Repositories;

use App\Inventory;
use App\InventoryAdjustment;
use App\Order;
use DB;
use Illuminate\Support\Collection;

class InventoryRepository {
	public function getAdjustments($page = 1, $limit = 25) {
		return InventoryAdjustment::with(['Inventory', 'Inventory.Product'])
			->orderBy('adjustment_datetime', 'DESC')
			->paginate($limit);
	}

	public function getAdjustmentsSearch($page = 1, $limit = 25, $search) {
		$inventory =  InventoryAdjustment::with(['Inventory', 'Inventory.Product'])
		->whereHas('Inventory.Product', function($query) use ($search) {
			$query->Where('name', 'LIKE', "%{$search}%");
		})
		->orWhere('order_id','LIKE',"%{$search}%")
		->orderBy('adjustment_datetime', 'DESC')
		->paginate($limit);

		return $inventory->appends ( array (
			'search' => $search
		  ) );
	}
	
	public function getSummary() {
		$onHand = $this->getOnHand();
		$pendingApproval = $this->getPendingApproval();
		$pendingPickup = $this->getPendingPickup();

		return new class($onHand, $pendingApproval, $pendingPickup) {
			public $onHand;
			public $pendingApproval;
			public $pendingPickup;

			public function __construct($onHand, $pendingApproval, $pendingPickup) {
				$this->onHand = $onHand;
				$this->pendingApproval = $pendingApproval;
				$this->pendingPickup = $pendingPickup;
			}
		};
	}

	public function getProductInventory() {
		$results = DB::table('inventory')
					->select([
						DB::raw('product.id product_id'),
						DB::raw('product.name product_name'),
						DB::raw('product_category.id product_category_id'),
						DB::raw('product_category.name product_category_name'),
						DB::raw('SUM(
							CASE
								WHEN inventory.txn_type = \'' . Inventory::TYPE_CREDIT . '\' THEN inventory.amount
								ELSE (-1 * CAST(inventory.amount AS SIGNED))
							END
						) on_hand'),
					])
					->join('product', 'inventory.product_id', '=', 'product.id')
					->join('product_category', 'product.product_category_id', '=', 'product_category.id')
					->groupBy(DB::raw('product.id, product.name, product_category.id, product_category.name'))
					->get();

		$summaries = [];
		foreach ($results as $product) {
			$summaries[$product->product_id] = $this->summarizeProduct($product);
		}

		$results = DB::table('order')
			->select([
				DB::raw('product_id'),
				DB::raw('SUM(
					CASE
						WHEN order.order_status = \'' . Order::STATUS_PENDING_APPROVAL . '\' THEN order_item.quantity
						ELSE 0
					END
				) pending_approval'),
				DB::raw('SUM(
					CASE
						WHEN order.order_status = \'' . Order::STATUS_PENDING_PICKUP . '\' THEN order_item.quantity
						ELSE 0
					END
				) pending_pickup'),
			])
			->join('order_item', 'order.id', '=', 'order_item.order_id')
			->groupBy('product_id')
			->get();

		foreach ($results as $product) {
			$summaries[$product->product_id]->pending_pickup = $product->pending_pickup;
			$summaries[$product->product_id]->pending_approval = $product->pending_approval;
		}

		return $summaries;
	}

	public function getOnHand() {
		$results = DB::table('inventory')
			->select([
				'product_category.name',
				DB::raw('SUM(
					CASE
						WHEN inventory.txn_type = \'' . Inventory::TYPE_CREDIT . '\' THEN inventory.amount
						ELSE (-1 *  CAST(inventory.amount AS SIGNED))
					END
				) quantity'),
			])
			->join('product', 'inventory.product_id', '=', 'product.id')
			->join('product_category', 'product.product_category_id', '=', 'product_category.id')
			->groupBy('product_category.name')
			->get();

		return $this->summarize($results);
	}

	public function getPendingApproval() {
		$results = DB::table('order')
			->select([
				'product_category.name',
				DB::raw('SUM(order_item.quantity) quantity'),
			])
			->join('order_item', 'order.id', '=', 'order_item.order_id')
			->join('product', 'order_item.product_id', '=', 'product.id')
			->join('product_category', 'product.product_category_id', '=', 'product_category.id')
			->where('order.order_status', '=', Order::STATUS_PENDING_APPROVAL)
			->groupBy('product_category.name')
			->get();

		return $this->summarize($results);
	}

	protected function getPendingPickup() {
		$results = DB::table('order')
			->select([
				'product_category.name',
				DB::raw('SUM(order_item.quantity) quantity'),
			])
			->join('order_item', 'order.id', '=', 'order_item.order_id')
			->join('product', 'order_item.product_id', '=', 'product.id')
			->join('product_category', 'product.product_category_id', '=', 'product_category.id')
			->where('order.order_status', '=', Order::STATUS_PENDING_PICKUP)
			->groupBy('product_category.name')
			->get();

		return $this->summarize($results);
	}

	protected function summarize(Collection $results) {
		$results = $results->pluck('quantity', 'name');
		return new class($results->get('Diaper') ?? 0, $results->get('Pull-up') ?? 0) {
			public $Diapers;
			public $Pullups;

			public function __construct($diapers, $pullups) {
				$this->Diapers = $diapers;
				$this->Pullups = $pullups;
			}
		};
	}

	protected function summarizeProduct($product) {
		return new class($product) {
			public $product_id;
			public $product_name;
			public $product_category_id;
			public $product_category_name;

			public $on_hand;
			public $pending_pickup;
			public $pending_approval;

			public function __construct($product) {
				$this->product_id = $product->product_id;
				$this->product_name = $product->product_name;
				$this->product_category_id = $product->product_category_id;
				$this->product_category_name = $product->product_category_name;
				$this->on_hand = $product->on_hand;
			}
		};
	}
}
