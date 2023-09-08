<?php namespace App\Http\Controllers\Admin\Fulfillment;

use App\PickupDate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;
use App\ViewGenerators\PickupDate\ExportedFulfillments;

class ExportedController extends BaseController {
	public function get(Request $Request) {
		$PickupDates = PickupDate::withFulfillmentsAfter(carbon());

		$PickupDates->load(
			// 'ManifestPdf',
			// 'LabelsPdf',
			'Fulfillment',
			// 'Fulfillment.ManifestPdf',
			// 'Fulfillment.LabelsPdf',
			'Fulfillment.Order.Agency',
			'Fulfillment.Order.ApprovedItem',
			'Fulfillment.Order.ApprovedItem.Product',
			// 'Fulfillment.Order.ApprovedItem.Product.Category',
			'Fulfillment.Order.ApprovedChild'
		);

		$ExportedFulfillments = new ExportedFulfillments; 
		$PickupDates = $ExportedFulfillments->prepareList($PickupDates);

		return view('admin.fulfillment.exported', [
			'PickupDates' => $PickupDates,
		]);
	}
}