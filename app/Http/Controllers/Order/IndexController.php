<?php namespace App\Http\Controllers\Order;

use App\Http\Controllers\Admin\Order\IndexController as AdminIndexController;
use App\Http\Controllers\Controller;
use App\Order;
use App\OrderSearch;
use App\Repositories\InventoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class IndexController extends AdminIndexController {

	protected function getView() {
		return 'order.index';
	}

	protected function getOrderSearch(Request $Request) {
		$Search = parent::getOrderSearch($Request);
		$Search->agency(Auth()->User()->Agency);

		return $Search;
	}

	public function get(Request $Request) {
		$OrderSearch = $this->getOrderSearch($Request)->orderBy('created_at', 'DESC');

		$Orders = $OrderSearch->get();
		$ProductSummary = $OrderSearch->getProductSummary();

		return view($this->getView(), [
			'Orders' => $Orders,
			'Pagination' => $OrderSearch->paginate()->appends($Request->except('page', '_url')),
			'ProductInventory' => $ProductSummary,
			'InventorySummary' => $this->InventoryRepository->getSummary(),
		]);
	}

	protected function setOrderSortForStatus($Search, $status) {
		$Search->orderBy('order.updated_at', 'DESC');
	}
}
