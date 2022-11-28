<?php namespace App\Http\Controllers\Admin\Fulfillment;

use App\Fulfillment;
use App\Http\Controllers\Controller as BaseController;
use App\Label\Manifest;
use App\PickupDate;
use Illuminate\Http\Request;

class ManifestController extends BaseController {
	public function get(Request $Request) {
		$Manifest = $this->makeManifest($Request);

		return $Manifest->render();
	}

	public function makeManifest($Request) {
		$date = $Request->get('date');

		if ($date) {
			return Manifest::makeForDate(PickupDate::find($date));
		} elseif ($batch = $Request->get('batch')) {
			return Manifest::makeForBatch(Fulfillment::find($batch));
		}

		abort(404);
	}
}