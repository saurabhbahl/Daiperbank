<?php namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller as BaseController;
use App\Order;
use App\PickupDate;
use App\ProductCategory;
use App\Child;
use Gate;
use Illuminate\Http\Request;

class CreateController extends BaseController {
	public function deny() {
		flash('You are not authorized to do that.')->error();
		return response()->redirect('order.index');
	}

	public function get(Request $Request, Order $Order) {
		if (Gate::denies('view', $Order)) {
			return $this->deny();
		}

		if (false == $Order->isDraft()) {
			return redirect()->route('order.view', [ $Order ]);
		}

		$PickupDates = PickupDate::getAvailableDates();
		$Children = auth()->user()->Agency->Children;
		$Children->load(['OrderItem', 'OrderItem.Product', 'OrderItem.Product.Category','OrderItem.Order']);
		$ProductCategories = ProductCategory::with('Product')->orderBy('name', 'ASC')->get();
		$Order->load(['Agency',
				'Child',
				'Child.Child',
				'Child.Child.Guardian',
				'Child.Item',
				'Child.Item.Product',
				'Child.Item.Product.Category', ]);

		return view('order.create', [
			'Order' => $Order,
			'Children' => $Children,
			'PickupDates' => $PickupDates,
			'ProductCategories' => $ProductCategories,
		]);
	}

	public function post(Request $Request, Order $Order) {
		if (Gate::denies('update', $Order)) {
			return $this->deny();
		}

		if ($Request->get('action') == 'submit') {
			if ( ! $Order->isDraft()) {
				flash('This order can not be submitted, as it is not a draft.')->error();
				return redirect()->route('order.view', [ $Order ]);
			}

			try {
				$this->validate($Request, $this->submitRules());

				$Order->update($Request->only('pickup_date_id'));
				$Order->submit();

				flash("Order #{$Order->full_id} has been submitted for review")->success();
				return redirect()->route('order.view', [ $Order ]);
			} catch (ValidationException $e) {
				flash('Could not submit order. Missing required information. Check below for more information.')->error();
				return redirect()->route('orders.create', [ $Order ]);
			} catch (Exception $e) {
				flash('An unexpected error occurred while trying to submit this order for review. Please try again.')->error();
				return redirect()->route('order.create', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'discard') {
			if ( ! $Order->isDraft()) {
				flash('This order can not be discarded, as it is not a draft.')->error();
				return redirect()->route('order.view', [ $Order ]);
			}

			try {
				$Order->discard();

				flash("Order {$Order->full_id} has been discarded.")->success();
				return redirect()->route('order.index');
			} catch (Exception $e) {
				flash('An unexpected error occurred while discarding this order. Please try aga.')->error();
				return redirect()->route('order.create', [ $Order ]);
			}
		}

		return redirect()->route('order.create', [ $Order ]);
	}

	protected function submitRules() {
		return [
			'pickup_date_id' => ['required', 'numeric', 'exists:pickup_date,id'],
		];
	}
}
