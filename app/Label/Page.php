<?php namespace App\Label;

use Illuminate\Support\Collection;

class Page extends Collection {

	protected $pdfFile;
	protected $starting_index = 0;

	public function filename($fn = null) {
		if ($fn) {
			$this->pdfFile = $fn;
			return $this;
		}

		return $this->pdfFile;
	}

	public function toPdfArray() {
		$count = $this->starting_index;
		return $this->reduce(function($fields, $Label) use (&$count) {
			return $fields + $Label->toPdfArray($count++);
		}, []);
	}
}