<?php namespace App\Reports;

use App\InventoryAdjustment;
use DB;

class DiaperDriveReport extends Report implements ReportContract {
	use WritesToTempFiles;

	public function getStats() {
		$adjustment_type = InventoryAdjustment::TYPE_DIAPER_DRIVE;
		$query = <<<EOQUERY
SELECT
	adjustment_datetime,
	adjustment_note,
	COALESCE(SUM(CASE WHEN i.product_id = 1 THEN i.amount ELSE 0 END), 0) preemie_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 2 THEN i.amount ELSE 0 END), 0) newborn_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 3 THEN i.amount ELSE 0 END), 0) size1_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 4 THEN i.amount ELSE 0 END), 0) size2_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 5 THEN i.amount ELSE 0 END), 0) size3_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 6 THEN i.amount ELSE 0 END), 0) size4_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 7 THEN i.amount ELSE 0 END), 0) size5_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 8 THEN i.amount ELSE 0 END), 0) size6_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 9 THEN i.amount ELSE 0 END), 0) size7_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 16 THEN i.amount ELSE 0 END), 0) other_diapers,
	COALESCE(SUM(CASE WHEN i.product_id = 10 THEN i.amount ELSE 0 END), 0) `2t-3t_pullups_boy`,
	COALESCE(SUM(CASE WHEN i.product_id = 11 THEN i.amount ELSE 0 END), 0) `3t-4t_pullups_boy`,
	COALESCE(SUM(CASE WHEN i.product_id = 12 THEN i.amount ELSE 0 END), 0) `4t-5t_pullups_boy`,
	COALESCE(SUM(CASE WHEN i.product_id = 13 THEN i.amount ELSE 0 END), 0) `2t-3t_pullups_girl`,
	COALESCE(SUM(CASE WHEN i.product_id = 14 THEN i.amount ELSE 0 END), 0) `3t-4t_pullups_girl`,
	COALESCE(SUM(CASE WHEN i.product_id = 15 THEN i.amount ELSE 0 END), 0) `4t-5t_pullups_girl`
FROM inventory_adjustment a
JOIN inventory i ON i.inventory_adjustment_id = a.id
JOIN product p on p.id = i.product_id

WHERE a.adjustment_type = {$adjustment_type}
AND adjustment_datetime BETWEEN ? AND ?

GROUP BY a.id

ORDER BY adjustment_datetime ASC
EOQUERY;

		return DB::select($query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59') ]);
	}

	public function getTempFilename() {
		return 'donationreport';
	}

	public function getName($type = 'csv') {
		$name = 'Diaper.Drive.Report.' . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [
			'Report Start Date',
			'Report End Date',
			'Diaper Drive Date',
			'Diaper Drive Note',
			'Preemie Diapers Received',
			'Newborn Diapers Received',
			'Size1 Diapers Received',
			'Size2 Diapers Received',
			'Size3 Diapers Received',
			'Size4 Diapers Received',
			'Size5 Diapers Received',
			'Size6 Diapers Received',
			'Size7 Diapers Received',
			'Other Diapers Received',
			'2t-3t Pullups Boy Received',
			'3t-4t Pullups Boy Received',
			'4t-5t Pullups Boy Received',
			'2t-3t Pullups Girl Received',
			'3t-4t Pullups Girl Received',
			'4t-5t Pullups Girl Received',
		];
		fputcsv($fp, $headers);
		foreach ($stats as $line) {
			$line = (array) $line;
			fputcsv($fp, [
				$this->start->format('Y-m-d'),
				$this->end->format('Y-m-d'),
				$line['adjustment_datetime'],
				$line['adjustment_note'],
				$line['preemie_diapers'],
				$line['newborn_diapers'],
				$line['size1_diapers'],
				$line['size2_diapers'],
				$line['size3_diapers'],
				$line['size4_diapers'],
				$line['size5_diapers'],
				$line['size6_diapers'],
				$line['size7_diapers'],
				$line['other_diapers'],
				$line['2t-3t_pullups_boy'],
				$line['3t-4t_pullups_boy'],
				$line['4t-5t_pullups_boy'],
				$line['2t-3t_pullups_girl'],
				$line['3t-4t_pullups_girl'],
				$line['4t-5t_pullups_girl'],
			]);
		}
		fclose($fp);
		return $this;
	}

	public function writeheader($fp) {
		fputcsv($fp, ['Report', 'Start Date', 'End Date', 'Generated Date']);
		fputcsv($fp, [
			'Diaper Drive Report',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	protected function getViewData() {
		$diaper_sizes = [
			'preemie' => 'P',
			'newborn' => 'NB',
		];
		foreach (range(1, 7) as $sz) {
			$diaper_sizes["size{$sz}"] = "Size {$sz}";
		}
		$diaper_sizes['other'] = 'Other diapers';

		$pullup_sizes = [
			'2t-3t' => '2T-3T',
			'3t-4t' => '3T-4T',
			'4t-5t' => '4T-5T',
		];

		return array_merge(parent::getViewData(), [
			'diaper_sizes' => $diaper_sizes,
			'pullup_sizes' => $pullup_sizes,
			'genders' => ['boy', 'girl'],
		]);
	}

	protected function getViewName() {
		return 'diaper-drive-report';
	}

	protected function getOrientation() {
		return 'Landscape';
	}
}
