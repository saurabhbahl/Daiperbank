<?php namespace App\Reports;

use WkHtmlToPdf\WkHtmlToPdf;

class LocationSubReport extends LocationOverview {
	public function setData($data) {
		$this->data = $data;
	}

	public function getData() {
		return $this->data;
	}

	public function getStats() {
		return $this->data;
	}

	public function toPdf($fn = null) {
		if ( ! $fn) {
			$fn = $this->makeTempFile($this->getTempFilename());
		}

		$PDF = new WkHtmlToPdf(config('hsdb.wkhtmltopdf.bin'));

		$PDF->setInputHtml($this->view()) //setInputPath($manifest_url)
			->setOutputPath($fn)
			->setFooterRightText('Page [page] of [toPage]')
			// ->setFooterLeftText($this->footerLabel())
			->disableSmartShrinking()
			->enableSmartShrinking()
			->resolveRelativeLinks()
			->setOrientation($this->getOrientation())
			->setPageSize('A4')
			// ->disableBackground()
			->convert();

		return $fn;
	}
}
