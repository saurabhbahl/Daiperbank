<?php

namespace App\Console\Commands;

use App\Fulfillment;
use App\PickupDate;
use DB;
use Illuminate\Console\Command;

class GeneratePackingLabels extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'hsdb:generate-packing-labels
                                { --batch= : Fulfillment Batch ID to generate PDF for (subset of a full fulfillment) }
                                { --date= : Pickup date (id or YYYY-mm-dd) to generate PDF for (will capture all orders exported for this date) }
                                { --filename= : Where to write the PDF (will not update database if set) }
                                { --with-fulfillments : If specifying pickup date to re-run, will also re-run and replace db records for all fulfillments on that pickup date. }';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Export packing labels for pickup dates or fulfillment batches';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$filename = $this->option('filename', null);

		if ($this->option('batch')) {
			$this->generateBatchLabels($this->option('batch'), $filename, ! ! $filename);
		}

		if ($this->option('date')) {
			$this->generatePickupDateLabels($this->option('date'), $filename, ! ! $filename);
		}
	}

	protected function generateBatchLabels($id, $filename, $silent) {
		$Batch = Fulfillment::findOrFail($id);

		return $Batch->generateLabels($filename, $silent);
	}

	protected function generatePickupDateLabels($id, $filename, $silent) {
		if (preg_match('#^\d+$#', $id)) {
			$PickupDate = collect(PickupDate::where('id', $id)->get());
		} else {
			$PickupDate = PickupDate::where(DB::raw('DATE(pickup_date)'), $id)->get();
		}

		if ($PickupDate->count() == 0) {
			throw new \Exception('No pickup date found.');
		}

		return $PickupDate->map(function ($PickupDate) use ($filename, $silent) {
			$PickupDate->generateLabels($filename, $silent);

			if ($this->option('with-fulfillments')) {
				$PickupDate->Fulfillment->map(function ($Fulfillment) {
					$Fulfillment->generateLabels(null, true);
				});
			}
		});
	}
}
