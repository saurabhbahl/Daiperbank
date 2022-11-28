<?php namespace App\Reports;

use App\Reports\Exceptions\InsufficientDataException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use WkHtmlToPdf\WkHtmlToPdf;

abstract class Report {
	protected $start;
	protected $end;
	protected $data;

	abstract protected function getViewName();

	abstract public function getStats();

	public function __construct(Carbon $start, Carbon $end) {
		$this->start = $start;
		$this->end = $end;
	}

	public function getTempFilename() {
		return Str::random();
	}

	public function run() {
		$this->data = $this->getStats();
		$this->validateData();
		return $this;
	}

	public function toCsv($fn = null) {
		if ( ! $fn) {
			$fn = $this->makeTempFile($this->getTempFilename());
		}

		$this->writeCsv($this->data, $fn);

		return $this;
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

	protected function getOrientation() {
		return 'Portrait';
	}

	public function getData() {
		return $this->data;
	}

	protected function getViewPath() {
		return 'admin.reporting.reports.';
	}

	protected function getView() {
		return $this->getViewPath() . $this->getViewName();
	}

	protected function getViewData() {
		return [
			'stats' => $this->getData(),
			'start' => $this->start,
			'end' => $this->end,
		];
	}

	public function view() {
		return view($this->getView(), $this->getViewData());
	}

	/**
	 * Detects if the data pulled from the run method is valid reportable data
	 *
	 * This is basically used to detect if a report is being run out of range which will
	 * return no report data, and cause the report output to be FUBR.
	 *
	 * @return $this
	 * @throws InsufficientDataException If the data is not valid, this exception is thrown
	 */
	protected function validateData() {
		$isValid = $this->isValid();

		if ( ! $isValid) {
			throw new InsufficientDataException;
		}

		return $this;
	}

	protected function isValid() {
		return $this->data && count($this->data) > 0;
	}
}
