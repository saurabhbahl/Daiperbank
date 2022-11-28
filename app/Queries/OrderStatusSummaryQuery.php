<?php namespace App\Queries;

use DB;
use App\Order;

class OrderStatusSummaryQuery extends Query {
	protected $ids = [];

	public function get() {
		return $this->buildQuery()->get()->first();
	}

	public function whereIdIn(array $ids = []) {
		$this->ids = $ids;
		return $this;
	}

	public function getSummary() {
		$results = $this->get();

		return new class($results->pending_approval, $results->pending_pickup) {
			public $pendingApproval;
			public $pendingPickup;

			public function __construct($pendingApproval, $pendingPickup) {
				$this->pendingApproval = $pendingApproval;
				$this->pendingPickup = $pendingPickup;
			}
		};
	}

	protected function buildQuery() {
		$this->Query = DB::table('order')
						->select([
							DB::raw('SUM(
								CASE WHEN order_status = \''. Order::STATUS_PENDING_APPROVAL . '\' THEN
									1
								ELSE 0
							END) pending_approval'),
							DB::raw('SUM(
								CASE WHEN order_status = \''. Order::STATUS_PENDING_PICKUP . '\' THEN
									1
								ELSE 0
							END) pending_pickup'),
						]);

		if ($this->ids) {
			$this->Query->whereIn('id', $this->ids);
		}

		return $this->Query;
	}

}