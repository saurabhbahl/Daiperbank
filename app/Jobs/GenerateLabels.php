<?php

namespace App\Jobs;

use App\Events\PackingLabelsGenerated;
use App\Fulfillment;
use App\Label\Label;
use App\Label\Page as LabelPage;
use App\PdfAsset;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use mikehaertl\pdftk\Pdf as PDF;

class GenerateLabels implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $Target;
	protected $LabelPages;
	protected $CurrentPage;
	protected $filename;
	protected $silent = false;

	/**
	 * Create a new job instance.
	 *
	 * @param   $Target Fulfillment|PickupDate  Object to generate labels for (If Fulfillment, only that batch will be generated. If PickupDate, a complete PDF of all batches will be created)
	 * @return void
	 */
	public function __construct($Target, $filename = null) {
		$this->Target = $Target;
		$this->filename = $filename;

		// TODO -- WRITE README FOR INSTALLING PHPTK FOR MAKING THIS STUFF WORK
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		set_time_limit(0);

		$Orders = $this->getOrders($this->Target);

		$this->LabelPages = collect([]);
		$this->makeLabelPages($Orders);
		$filename = $this->outputFilename();
		$this->generateLabels(storage_path($filename));

		if ( ! $this->silent) {
			event(new PackingLabelsGenerated($this->Target, new PdfAsset([
				'filename' => $filename,
				'asset_type' => PdfAsset::TYPE_LABELS,
			])));
		}
	}

	public function silent($silent = false) {
		$this->silent = $silent;
	}

	protected function productsPerLabel() {
		return config('hsdb.labels.products_per_label');
	}

	protected function labelsPerPage() {
		return config('hsdb.labels.per_page');
	}

	protected function templateFile() {
		return resource_path('assets/pdf/' . config('hsdb.labels.template'));
	}

	protected function getOrders($Target) {
		if ($Target instanceof Fulfillment) {
			return $Target->Order;
		}
		return $this->getPickupDateOrders($Target);
	}

	protected function getPickupDateOrders($Target) {
		return $Target->Fulfillment->reduce(function ($Orders, $Batch) use ($Target) {
			return $Orders->concat($this->getOrders($Batch));
		}, collect([]));
	}

	protected function makeLabelPages(Collection $Orders) {
		$Orders->map(function ($Order) {
			$this->addLabelsForOrder($Order);
		});

		return $this->LabelPages;
	}

	protected function addLabelsForOrder($Order) {
		$Order->ApprovedChild->map(function ($Child) use ($Order) {
			$order_number = "{$Order->id}";
			// $Label = new Label($order_number, $Order->Agency->name);
			$Label = new Label($order_number, $Order->Agency->id_prefix);
			$Label->addChild($Child);
			if($Child->Child['attributes']['is_menstruator']){
				$new_name=$Label->child().' (Menstruator)';
				$Label->child($new_name);
				// var_dump($Label->child());
			}
			// $Label->addChild($Child);

			$this->addLabel($Label);
		});
		// $label_count = ceil($Order->ApprovedItem->count() / $this->productsPerLabel());
		// $Order->AggregateItems()->chunk($this->productsPerLabel())->map(function($Items, $idx) use ($Order, $label_count) {
		//     $order_number = "#{$Order->full_id}";

		//     if ($label_count > 1) {
		//         $order_number .= " (" . ($idx + 1) . "/{$label_count}" . ")";
		//     }

		//     $Label = new Label($order_number, $Order->Agency->name);
		//     $Items->map(function($Item) use ($Label) {
		//         $Label->addProduct($Item->Product->full_name, $Item->quantity);
		//     });

		//     $this->addLabel($Label);
		// });

		return $this->LabelPages;
	}

	protected function addLabel(Label $Label) {
		$CurrentPage = $this->getCurrentPage();

		if ($CurrentPage->count() >= $this->labelsPerPage()) {
			$CurrentPage = $this->newPage();
		}

		$CurrentPage->push($Label);

		return $this;
	}

	protected function generateLabels($filename) {
		$this->LabelPages->map(function ($Page, $idx) {
			$this->makeTmpLabelFile($Page);
		});

		return $this->combineLabelPages($filename);
	}

	protected function makeTmpLabelFile($Page) {
		$PDF = new PDF($this->templateFile());

		if ( ! file_exists(storage_path('cache/pdf'))) {
			mkdir(storage_path('cache/pdf'), 0755, true);
		}

		$PDF->fillForm( $Page->toPdfArray() )
			->flatten()
			->saveAs($filename = storage_path('cache/pdf/' . uniqid('labels.', true)));

		$Page->filename($filename);

		return $Page;
	}

	protected function combineLabelPages($filename) {
		$PDF = new PDF;

		$PDF = $this->LabelPages->reduce(function ($PDF, $Page) {
			return $PDF->addFile($Page->filename());
		}, new PDF);

		$PDF->saveAs($filename);

		$this->LabelPages->map(function ($Page) {
			unlink($Page->filename());
		});

		return $filename;
	}

	protected function outputFilename() {
		if ( ! $this->filename) {
			return PdfAsset::outputFilename($this->Target, PdfAsset::TYPE_LABELS);
		}
		return $this->filename;
	}

	protected function getCurrentPage() {
		if ($this->CurrentPage) {
			return $this->CurrentPage;
		}

		return $this->newPage();
	}

	protected function newPage() {
		$this->LabelPages->push( $this->CurrentPage = new LabelPage );

		return $this->CurrentPage;
	}
}
