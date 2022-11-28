<?php namespace App\Http\Controllers\Order;

use Gate;
use App\Order;
use App\PickupDate;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Queries\OrderProductSummaryQuery;

class PrintController extends Controller {
	public function get(Request $Request, Order $Order) {
		if (Gate::denies('view', $Order)) {
			return $this->deny();
		}

		$Order->load('Agency', 'Note', 'Note.Author', 'Child', 'Child.Child', 'Child.Child.Guardian', 'Child.Item', 'Child.Item.Product', 'Child.Item.Product.Category');
		$Children = $Order->Child->sort(function($Left, $Right) {
			return $Left->Child->name <=> $Right->Child->name;
		});

		$ProductSummary = OrderProductSummaryQuery::create()->orderIds(
			[$Order->id]
		)->getSummary();

		return view('order.view-print', [
			'Order' => $Order,
			'Children' => $Children,
			'Categories' => ProductCategory::with(['Product'])->orderBy('id', 'ASC')->get(),
			'PickupDates' => PickupDate::orderBy('pickup_date', 'DESC')->get(),
			'ProductSummary' => $ProductSummary,
		]);
	}
}
