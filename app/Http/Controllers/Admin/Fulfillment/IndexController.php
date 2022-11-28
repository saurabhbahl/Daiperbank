<?php namespace App\Http\Controllers\Admin\Fulfillment;

use App\Fulfillment;
use App\Http\Controllers\Controller as BaseController;
use App\PickupDate;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IndexController extends BaseController {
	public function get(Request $Request) {
		try {
			$this->validate($Request, $this->rules());
			$current_pickup_date = $Request->get('date', null);
		}
		catch (Exception $e) {
			$current_pickup_date = null;
		}

		$PickupDates = PickupDate::getPickupDatesWithPendingFulfillments();
		$PickupDate = null;
		$Orders = null;
		$FilledOrders = null;
		$PreviousBatches = null;

		if ($PickupDates->count()) {
			if ($current_pickup_date) {
				$PickupDate = $PickupDates->findFor($current_pickup_date);

				if (!$PickupDate) {
					// couldn't find pickup for the requested date, so try to find one for afterwards.
					$PickupDate = $PickupDates->findFirstAfter($current_pickup_date);

					// not sure what i was trying to accomplish on this line...i feel like the first call to findFor did the same thing..
					// $PickupDate = PickupDate::where(DB::raw('DATE(pickup_date)'), '=', $current_pickup_date)->firstOrFail();
				}
			}

			if (!$PickupDate) {
				$PickupDate = $PickupDates->findFirstAfter(carbon()->format("Y-m-d"));

				if (!$PickupDate) {
					$PickupDate = $PickupDates->first();
				}
			}

			if ($PickupDate) {
				$Orders = Fulfillment::getUnexportedOrders($PickupDate);
				$FilledOrders = Fulfillment::getFilledOrders($PickupDate);
				$PreviousBatches = Fulfillment::with('Order', 'Order.Agency')
									->where('pickup_date_id', $PickupDate->id)
									->get();
			}
		}

		return view('admin.fulfillment.index', [
			'PickupDates' => $PickupDates,
			'PickupDate' => $PickupDate,
			'Orders' => $Orders,
			'FilledOrders' => $FilledOrders,
			'PreviousBatches' => $PreviousBatches,
		]);
	}

	public function post(Request $Request) {
		try {
			$this->validate($Request, $this->rules());
			$pickup_date = $Request->get('date');

			$PickupDate = PickupDate::where(DB::raw('DATE(pickup_date)'), '=', $pickup_date)->firstOrFail();
			$Fulfillment = Fulfillment::createFromDate($PickupDate);

			$Fulfillment->generatePdfs();

			flash("{$Fulfillment->Order()->count()} Orders exported. Fulfillment resources are now being generated.")->success();

			// TODO send the user to a detail page for the fulfillment, where orders are listed + inventory counts are summarized, and links for label PDFs are available
			return redirect()->route('admin.fulfillment.exported');

		}
		catch (ValidationException $e) {
			flash("The date you selected is not a valid scheduled pick up date.")->error();
			return redirect()->back();
		}
		catch (Exception $e) {
			throw $e;
			flash("An error occurred while creating this fulfillment. Please try again. {$e->getMessage()}")->error();
			return redirect()->back();
		}
	}

	protected function rules() {
		return [
			'date' => ['nullable', 'date_format:Y-m-d'],
		];
	}
}