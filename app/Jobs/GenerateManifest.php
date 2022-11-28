<?php

namespace App\Jobs;

use App\Events\OrderManifestGenerated;
use App\Fulfillment;
use App\Label\Manifest;
use App\PdfAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use WkHtmlToPdf\WkHtmlToPdf;

class GenerateManifest implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $Target;
	protected $Orders;
	protected $silent = false;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($Target) {
		$this->Target = $Target;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		set_time_limit(0);

		$Manifest = $this->makeManifest($this->Target);

		$PDF = new WkHtmlToPdf(config('hsdb.wkhtmltopdf.bin'));

		$PDF->setInputHtml($Manifest->render()) //setInputPath($manifest_url)
			->setOutputPath(storage_path($filename = $this->outputFilename()))
			->setFooterRightText('Page [page] of [toPage]')
			->setFooterLeftText($this->footerLabel())
			->disableSmartShrinking()
			->enableSmartShrinking()
			->resolveRelativeLinks()
			->setOrientation('Landscape')
			->setPageSize('A4')
			->disableBackground()
			->convert();

		event(new OrderManifestGenerated($this->Target, new PdfAsset([
			'filename' => $filename,
			'asset_type' => PdfAsset::TYPE_MANIFEST,
	   ])));
	}

	protected function outputFilename() {
		return PdfAsset::outputFilename($this->Target, PdfAsset::TYPE_MANIFEST);

		// $fn = Str::finish(trim(config('hsdb.storage_folder'), '/'), '/') . trim($this->Target->generateManifestFilename(), '/');
		// $dirname = dirname($fn);

		// if (!file_exists(storage_path($dirname))) {
		//     mkdir(storage_path($dirname), 0755, true);
		// }

		// return $fn;
	}

	protected function makeManifest($Target) {
		if ($Target instanceof Fulfillment) {
			return Manifest::makeForBatch($Target);
			// return route('manifest', [ 'batch' => $Target->id ]);
		}

		return Manifest::makeForDate($Target);
		// return route('manifest', [ 'date' => $Target->id ]);
	}

	protected function footerLabel() {
		if ($this->Target instanceof Fulfillment) {
			return $this->batchFooterLabel();
		}
		return $this->pickupDateFooterLabel();
	}

	protected function batchFooterLabel() {
		return 'Order Manifest for ' . $this->Target->PickupDate->pickup_date->format('D, M j, Y') . ' - Batch ' . $this->Target->id . ' - Generated at ' . carbon()->format('m/d/y h:ia');
	}

	protected function pickupDateFooterLabel() {
		return 'Order Manifest for ' . $this->Target->pickup_date->format('D, M j, Y') . ' - Generated at ' . carbon()->format('m/d/y h:ia');
	}
}
