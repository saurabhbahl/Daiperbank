<?php namespace App\Queries;

use App\Inventory;
use App\Order;
use App\Product;
use DB;

class ProductInventoryQuery extends Query {

	public function get() {
		$Products = Product::with(['Category'])
						->orderBy('product_category_id', 'ASC')
						->orderBy('id', 'ASC')
						->get()
						->keyBy('id');


		$this->buildQuery()->get()->map(function($result) use ($Products) {
			$Product = $Products->get($result->product_id);
			$Product->Inventory = $this->summarizeProduct($result);
		});

		return $Products;
	}

	public function buildQuery() {
		$this->Query = DB::table('inventory as i')
					->select([
						'i.product_id',
						DB::raw('SUM(
							CASE
								WHEN i.txn_type = \'' . Inventory::TYPE_CREDIT . '\' THEN i.amount
								ELSE (-1 * CAST(i.amount AS SIGNED))
							END
						) on_hand'),
					])
					->join('product', 'i.product_id', '=', 'product.id')
					->groupBy('i.product_id')
					->orderBy('product.product_category_id', 'asc')
					->orderBy('product.id', 'asc');

		return $this->Query;
	}

	protected function summarizeProduct($result) {
		return new class($result) {
			public $on_hand;
			public $pending_approval;
			public $pending_pickup;
			public $ordered;

			public function __construct($result) {
				$this->on_hand = $result->on_hand;
			}
		};
	}
}