<?php namespace App;

use Zipper;

class AssetArchive {
	protected $tmpFilename;
	protected $Target;
	protected $Zip;

	public function __construct($Target) {
		$this->Target = $Target;
		$this->tmpFilename = $this->tmpFilename();
		$this->Zip = Zipper::make($this->tmpFilename);
		$this->addFolder();
	}

	public function zipPath() {
		return $this->tmpFilename;
	}

	public function downloadFilename() {
		return $this->outputFolderName() . '.zip';
	}

	protected function addFolder() {
		$this->Zip->folder( $this->outputFolderName() );

		return $this;
	}

	protected function addFile($relativePath, $friendlyName) {
		$this->Zip->add(storage_path($relativePath), $friendlyName);

		return $this;
	}

	protected function close() {
		$this->Zip->close();

		return $this;
	}

	protected function outputFolderName() {
		if ($this->Target instanceof Fulfillment) {
			return 'Pickup ' . $this->Target->PickupDate->pickup_date->format('Y-m-d') . ' - Batch ' . $this->Target->id . ' - ' . carbon()->format('Ymd.Hi');
		}

		return 'Pickup ' . $this->Target->pickup_date->format('Y-m-d') . ' - All Orders - ' . carbon()->format('Ymd.Hi');
	}

	protected function tmpFilename() {
		return storage_path('cache/pdf/' . uniqid('archive.', true));
	}

	public static function createFromBatch(Fulfillment $Batch) {
		if ( ! $Batch->verifyAssetsDownloadable()) {
			$Batch->generatePdfs();
			return null;
		}

		return (new static($Batch))
				->addFile($Batch->LabelsPdf->filename, $Batch->LabelsPdf->friendlyName())
				->addFile($Batch->ManifestPdf->filename, $Batch->ManifestPdf->friendlyName())
				->close();
	}

	public static function createFromPickupDate(PickupDate $PickupDate) {
		if ( ! $PickupDate->verifyAssetsDownloadable()) {
			$PickupDate->generatePdfs();
			return null;
		}

		return (new static($PickupDate))
					->addFile($PickupDate->LabelsPdf->filename, $PickupDate->LabelsPdf->friendlyName())
					->addFile($PickupDate->ManifestPdf->filename, $PickupDate->ManifestPdf->friendlyName())
					->close();
	}
}
