<?php namespace App\Label;

use App\PickupDate;
use Illuminate\Support\Collection;

class Manifest {
	protected $PickupDate;
	protected $Batch;
	protected $Orders;

	public function __construct(PickupDate $PickupDate, Collection $Orders, $Batch = null) {
		$this->PickupDate = $PickupDate;
		$this->Orders = $Orders;
		$this->Batch = $Batch;
	}

	public function render() {
		return view('admin.order.manifest', [
			'PickupDate' => $this->PickupDate,
			'Orders' => $this->Orders,
		])->render();
	}

	static public function makeForDate($PickupDate) {
		return new static($PickupDate, static::getOrdersForDate($PickupDate));
	}

	static public function makeForBatch($Batch) {
		return new static(static::getPickupDateForBatch($Batch), static::getOrdersForBatch($Batch), $Batch);
	}

	static protected function getOrdersForDate($PickupDate) {
		$PickupDate->load('Fulfillment', 'Fulfillment.Order', 'Fulfillment.Order.Child', 'Fulfillment.Order.ApprovedChild', 'Fulfillment.Order.Item');
		return $PickupDate->Fulfillment->reduce(function($Orders, $Batch) {
			return $Orders->concat(static::getOrdersForBatch($Batch));
		}, collect([]));
	}

	static protected function getPickupDateForBatch($Batch) {
		return $Batch->PickupDate;
	}

	static protected function getOrdersForBatch($Batch) {
		return $Batch->Order;
	}
}