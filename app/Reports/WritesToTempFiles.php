<?php namespace App\Reports;

trait WritesToTempFiles {
	protected $filename;

	protected function makeTempfile($prefix) {
		$this->filename = @tempnam('C:\Users\Public\tmp', $prefix);
		return $this->filename;
	}

	public function getFilename() {
		return $this->filename;
	}
}