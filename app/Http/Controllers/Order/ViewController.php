<?php namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller as BaseController;
use App\Order;
use App\PickupDate;
use App\ProductCategory;
use Gate;
use Illuminate\Http\Request;

class ViewController extends BaseController {
	public function deny() {
		flash("You are not authorized to do that.")->error();
		return redirect()->route('order.index');
	}

	public function get(Request $Request, Order $Order) {
		if (Gate::denies('view', $Order)) {
			return $this->deny();
		}

		$Order->load('Agency', 'Note', 'Note.Author', 'Child', 'Child.Child', 'Child.Child.Guardian', 'Child.Item', 'Child.Item.Product', 'Child.Item.Product.Category');

		return view('order.view', [
			'Order' => $Order,
			'Categories' => ProductCategory::with(['Product'])->orderBy('id', 'ASC')->get(),
			'PickupDates' => PickupDate::orderBy('pickup_date', 'DESC')->get(),
		]);
	}

	public function post(Request $Request, Order $Order) {
		if ($Request->get('action') == 'clone') {
			if (Gate::denies('create', $Order)) {
				return $this->deny();
			}

	 		try {
	 			$ClonedOrder = $Order->clone();

				flash("Order has been cloned. New order id: #{$ClonedOrder->full_id}")->success();
	 			return redirect()->route('order.create', [ $ClonedOrder ]);
	 		}
	 		catch (Exception $e) {
	 			flash("An error occurred while trying to clone this order.")->error();
	 			return redirect()->route('order.view', [ $Order ]);
	 		}
	 	}
	 	elseif ($Request->get('action') == 'cancel') {
	 		if ( ! $Order->isPending() && ! $Order->isDraft()) {
	 			flash("You can not cancel this order.")->error();
	 			return redirect()->route('order.view', [$Order]);
	 		}

	 		try {
	 			$Cancelled = $Order->cancel()->silent()->cancel();

				flash("Order #{$Cancelled->full_id} has been cancelled.")->success();
	 			return redirect()->route('order.index');
	 		}
	 		catch (Exception $e) {
	 			flash("An error occurred while trying to cancel this order.")->error();
	 			return redirect()->route('order.view', [ $Order ]);
	 		}
	 	}

	 	return redirect()->route('order.view', [ $Order ]);
	}
}