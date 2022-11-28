<?php namespace App\Reports;

interface ReportContract {
	public function run();

	public function toPdf();

	public function toCsv();

	public function getFilename();

	public function getName($type = 'csv');
}
