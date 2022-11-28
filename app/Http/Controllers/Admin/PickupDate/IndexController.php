<?php namespace App\Http\Controllers\Admin\PickupDate;

use App\Http\Controllers\Controller as BaseController;
use App\PickupDate;
use Illuminate\Http\Request;

class IndexController extends BaseController {

	public function get(Request $Request) {
		$month = $Request->get('month');
		$this->validate($Request, $this->rules());

		if (!$month) {
			$month = carbon()->format('Y-m');
		}

		$PickupDates = PickupDate::getPickupDatesByDate($month);

		return view('admin.pick-up.index', [
			'PickupDates' => $PickupDates,
			'list_month' => $month,
		]);
	}

	public function rules() {
		return [
			'month' => ['pickup_date_month_selector'],
		];
	}
}