<?php namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Order;
use App\OrderSearch;
use App\Repositories\InventoryRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;

class IndexController extends Controller {
	public function __construct(
		OrderRepository $OrderRepository,
		ProductRepository $ProductRepository,
		InventoryRepository $InventoryRepository
	) {
		$this->OrderRepository = $OrderRepository;
		$this->ProductRepository = $ProductRepository;
		$this->InventoryRepository = $InventoryRepository;
	}

	public function get(Request $Request) {
		$OrderSearch = $this->getOrderSearch($Request);

		$selected_order_status = $Request->get('status', 'pending_approval');
		if ($Request->has('filter')) {
			$selected_order_status = null;
		} else {
			if ($selected_order_status != 'all') {
				$OrderSearch->status($selected_order_status);
			}

			$this->setOrderSortForStatus($OrderSearch, $selected_order_status);
		}

		$Orders = $OrderSearch->get();
		// $Summary = $OrderSearch->getSummary();
		$ProductSummary = $OrderSearch->getProductSummary();

		return view($this->getView(), [
			'selected_order_status' => $selected_order_status,
			'Orders' => $Orders,
			// 'Summary' => $Summary,
			'Pagination' => $OrderSearch->paginate()->appends($Request->except('page', '_url')),
			'ProductInventory' => $ProductSummary,
			'InventorySummary' => $this->InventoryRepository->getSummary(),
		]);
	}

	protected function getView() {
		return 'admin.order.index';
	}

	protected function getOrderSearch(Request $Request) {
		$OrderSearch = new OrderSearch;

		if ($Request->input('filter.search')) {
			$OrderSearch->search($Request->input('filter.search'));
		}

		if ($Request->input('filter.status')) {
			$OrderSearch->status($Request->input('filter.status'));

			$this->setOrderSortForStatus($OrderSearch, $Request->input('filter.status'));
		}

		if ($Request->input('filter.start_date')) {
			$OrderSearch->startDate($Request->input('filter.start_date'));
		}

		if ($Request->input('filter.end_date')) {
			$OrderSearch->endDate($Request->input('filter.end_date'));
		}

		if ($Request->input('per_page')) {
			$OrderSearch->perPage($Request->input('per_page'));
		}

		if ($Request->input('page')) {
			$OrderSearch->page($Request->input('page'));
		}

		return $OrderSearch;
	}

	protected function setOrderSortForStatus($Search, $status) {
		switch ($status) {
			case 'pending_approval':
			case 'pending_pickup':
				$Search->orderBy('pickup_date.pickup_date', 'ASC');
				break;

			case 'draft':
				$Search->orderBy('order.created_at', 'DESC');
				break;

			case 'cancelled':
			case 'rejected':
				$Search->orderBy('order.updated_at', 'DESC');
				break;

			case 'fulfilled':
				$Search->orderBy('pickup_date.pickup_date', 'DESC');
				break;

			default:
				$Search->orderBy('order.id', 'DESC');
				break;
		}

		return $Search;
	}
}
