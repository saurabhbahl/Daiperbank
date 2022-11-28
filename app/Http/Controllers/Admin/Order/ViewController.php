<?php namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Order;
use App\PickupDate;
use App\ProductCategory;
use Illuminate\Http\Request;

class ViewController extends Controller {
	public function get(Request $Request, Order $Order) {
		$Order->load('Agency', 'Note', 'Note.Author', 'Child', 'Child.Child', 'Child.Child.Guardian', 'Child.Item', 'Child.Item.Product', 'Child.Item.Product.Category');

		return view('admin.order.view', [
			'Order' => $Order,
			'isAdmin' => true,
			'Categories' => ProductCategory::with(['Product'])->orderBy('id', 'ASC')->get(),
			'PickupDates' => PickupDate::orderBy('pickup_date', 'DESC')->get(),
		]);
	}

	public function post(Request $Request, Order $Order) {
		if ($Request->get('action') == 'approve') {
			try {
				$this->validate($Request, $this->rules());

				if ( ! $Order->isPending()) {
					flash('This order can not be approved, as it is not awaiting approval.')->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				}

				if ($Order->pickup_date_id != $Request->get('pickup_date_id')) {
					$Order->reschedule()->for( PickupDate::findOrFail($Request->get('pickup_date_id')) )->silent()->save();
				}

				$ApprovedOrder = $Order->approve()->approve();

				if ( ! $Order->PickupDate->isReconciled()) {
					flash("Order #{$ApprovedOrder->full_id} has been approved.")->success();
				} else {
					// orders are automatically fulfilled if they're approved for previously
					// reconciled pickup dates;
					$FulfilledOrder = $Order->fulfill();
					flash("Order #{$Order->full_id} has been fulfilled, as the selected pickup date has already been reconciled.")->success();
				}
				return redirect()->route('admin.order.index');
			} catch (Exception $e) {
				flash('An error occurred while trying to approve this order.')->success();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'reject') {
			try {
				if ( ! $Order->isPending() && ! $Order->isRejected()) {
					flash('This order can not be rejected, as it is not awaiting approval.')->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				}

				if ( ! $Order->isRejected()) {
					$RejectedOrder = $Order->reject()
						->reason($Request->get('reason', null))
						->reject();
				}

				flash("Order #{$Order->full_id} has been rejected.")->success();
				return redirect()->route('admin.order.index');
			} catch (Exception $e) {
				flash('An error occurred while trying to reject this order.')->error();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'cancel') {
			try {
				if ( ! $Order->isApproved()) {
					if ($Order->isCancelled()) {
						flash('This order has already been cancelled.')->error();
					} elseif ($Order->isFulfilled()) {
						flash('You can not cancel a fulfilled order.')->error();
					} else {
						flash('Only orders that have been approved and are awaiting pickup may be cancelled.')->error();
					}

					return redirect()->route('admin.order.view', [ $order ]);
				}

				$Cancelled = tap(
						$Order->cancel()
						->reason($Request->get('reason', null))
					)->cancel();

				flash("Order #{$Cancelled->full_id} has been cancelled.")->success();
				return redirect()->route('admin.order.index');
			} catch (Exception $e) {
				flash('An error occurred while trying to cancel this order.')->error();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'return') {
			try {
				if ( ! $Order->isFulfilled()) {
					flash('Only fulfilled orders may be returned.')->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				}

				$Return = $Order->cancel()
							->reason($Request->get('reason', null))
							->silent()
							->cancel();

				flash("Order #{$Return->full_id} has been returned.")->success();
				return redirect()->route('admin.order.index');
			} catch (Exception $e) {
				flash('An error occurred while trying to return this order.')->error();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'clone') {
			try {
				$ClonedOrder = $Order->clone();

				flash("Order has been cloned. New order id: #{$ClonedOrder->full_id}")->success();
				return redirect()->route('admin.order.view', [ $ClonedOrder ]);
			} catch (Exception $e) {
				flash('An error occurred while trying to clone this order.')->success();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'submit') {
			try {
				$this->validate($Request, $this->rules());

				if ( ! $Order->isDraft()) {
					flash('Orders may only be submitted when they are drafts.')->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				} elseif ( $Order->canBeSubmitted()) {
					flash('This order can not be submitted yet, it is still missing required information.')->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				}

				$Order->update([
					'pickup_date_id' => $Request->get('pickup_date_id'),
				]);

				$Order->refresh();

				$PickupDate = $Order->PickupDate;

				if ($PickupDate->pickup_date->lt(carbon())) {
					// selected pickup date is prior to today's date, which means we are going to
					// auto-fulfill this puppy.
					$Order->approve()->silent()->approve()->fulfill();
					flash("Order #{$Order->full_id} has been submitted and recorded as Fulfilled due to back-dating the pickup date.")->success();
				} else {
					$Submitted = $Order->submit();
					flash("Order #{$Submitted->full_id} has been submitted")->success();
				}

				return redirect()->route('admin.order.view', [ $Order ]);
			} catch (Exception $e) {
				flash('An error occurred while submitting this order.')->error();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'discard') {
			try {
				$Discarded = $Order->discard();

				if ( ! $Order->isDraft()) {
					flash('Only draft ordres may be discarded.')->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				}

				flash("Order #{$Discarded->full_id} has been discarded.")->success();
				return redirect()->route('admin.order.index');
			} catch (Exception $e) {
				flash('An error occurred while discarding this order.')->error();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		} elseif ($Request->get('action') == 'reschedule') {
			try {
				$this->validate($Request, $this->rules());
				$pickup_date_id = $Request->get('pickup_date_id');

				if ( ! $Order->isApproved()) {
					flash("Orders may only be rescheduled while they're awaiting pickup.")->error();
					return redirect()->route('admin.order.view', [ $Order ]);
				}

				if ($Order->pickup_date_id != $pickup_date_id) {
					$RescheduledOrder = $Order->reschedule()->for( PickupDate::findOrFail($pickup_date_id) );

					if ( ! $Order->isApproved()) {
						$RescheduledOrder->silent();
					}

					$RescheduledOrder->save();

					flash('Order has been successfully rescheculed.')->success();
				} else {
					flash('Order was not rescheduled. The same date was selected.')->warning();
				}

				return redirect()->route('admin.order.view', [ $Order ]);
			} catch (Exception $e) {
				flash('An error occurred while trying to approve this order.')->error();
				return redirect()->route('admin.order.view', [ $Order ]);
			}
		}
	}

	protected function rules() {
		return [
			'pickup_date_id' => ['required', 'exists:pickup_date,id'],
		];
	}
}
