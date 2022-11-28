<?php namespace App\Queries;

use App\Order;
use App\ProductCategory;
use DB;

class OrderSummaryQuery extends Query {
	protected $ids = [];

	public function get() {
		return $this->buildQuery()->get();
	}

	public function whereIdIn(array $ids = []) {
		$this->ids = $ids;
		return $this;
	}

	public function getSummary() {
		return $this->get()->keyBy('order_id');
	}

	protected function buildQuery() {
		$this->Query = DB::table('order as o')
						->select([
							'o.id as order_id',
							DB::raw('SUM(
								CASE WHEN p.product_category_id = ' . ProductCategory::CATEGORY_ID_DIAPERS . ' THEN
									i.quantity
								ELSE 0
							END) diapers'),
							DB::raw('SUM(
								CASE WHEN p.product_category_id = ' . ProductCategory::CATEGORY_ID_PULLUPS. ' THEN
									i.quantity
								ELSE 0
							END) pullups'),
							DB::raw('( SELECT COUNT(DISTINCT id) FROM order_child
							WHERE order_id = o.id ) children'),
						])
						->join('order_item as i', 'i.order_id', '=', 'o.id', 'left')
						->join('product as p', 'p.id', '=', 'i.product_id', 'left')
						->groupBy('o.id');

		if ($this->ids) {
			$this->Query->whereIn('o.id', $this->ids);
		}

		return $this->Query;
	}

}