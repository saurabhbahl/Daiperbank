<?php namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Queries\OrderProductSummaryQuery;
use App\Repositories\InventoryRepository;
use Illuminate\Http\Request;

class IndexController extends Controller {
	public function __construct(InventoryRepository $Inventory) {
		$this->InventoryRepository = $Inventory;
	}

	public function get(Request $Request) {
		if ($Request->input("search")) {
			$Adjustments = $this->InventoryRepository->getAdjustmentsSearch($Request->get('page', 1), 10,$Request->get("search"));
		}
		else{
			$Adjustments = $this->InventoryRepository->getAdjustments($Request->get('page', 1), 10);
		}
		$InventorySummary = $this->InventoryRepository->getSummary();

		return view('admin.inventory.index', [
			'Adjustments' => $Adjustments,
			'Summary' => $InventorySummary,
			'ProductInventory' => with(new OrderProductSummaryQuery)->getSummary(),
		]);
	}
}