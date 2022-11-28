<?php namespace App\Http\Controllers\Admin\Fulfillment;

use App\Fulfillment;
use App\Http\Controllers\Controller as BaseController;
use App\PickupDate;
use Illuminate\Http\Request;

class AssetController extends BaseController {
	public function batch_download_all(Request $Request, Fulfillment $Batch) {
		$AssetArchive = $Batch->createAssetArchive();

		if ($AssetArchive) {
			return response()->download($AssetArchive->zipPath(), $AssetArchive->downloadFilename());
		}

		return view('admin.fulfillment.asset-download');
	}

	public function pickupdate_download_all(Request $Request, PickupDate $PickupDate) {
		$AssetArchive = $PickupDate->createAssetArchive();

		if ($AssetArchive) {
			return response()->download($AssetArchive->zipPath(), $AssetArchive->downloadFilename());
		}

		return view('admin.fulfillment.asset-download');
	}
}
