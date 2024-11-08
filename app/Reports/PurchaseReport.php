<?php namespace App\Reports;

use App\InventoryAdjustment;
use DB;

class PurchaseReport extends Report implements ReportContract {
	use WritesToTempFiles;

	public function getStats() {
		$adjustment_type = InventoryAdjustment::TYPE_PURCHASE;
		$aggregate_query = <<<EOQUERY
SELECT
	COUNT(DISTINCT a.id) purchase,
	COALESCE(SUM(CASE WHEN (i.product_id < 10 OR i.product_id = 16) THEN i.amount ELSE 0 END), 0) total_diapers,
	COALESCE(SUM(CASE WHEN i.product_id BETWEEN 10 AND 15 THEN i.amount ELSE 0 END), 0) total_pullups,
	COALESCE(SUM(CASE WHEN i.product_id BETWEEN 17 AND 32 THEN i.amount ELSE 0 END), 0) total_periods,
	COALESCE(SUM(i.amount), 0) total_donated,
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
	COALESCE(SUM(CASE WHEN i.product_id = 15 THEN i.amount ELSE 0 END), 0) `4t-5t_pullups_girl`,
	COALESCE(SUM(CASE WHEN i.product_id = 17 THEN i.amount ELSE 0 END), 0) `pads_only_packet`,
	COALESCE(SUM(CASE WHEN i.product_id = 18 THEN i.amount ELSE 0 END), 0) `tampons_and_pads_packet`,
	COALESCE(SUM(CASE WHEN i.product_id = 19 THEN i.amount ELSE 0 END), 0) `postpartum_packet`,
	COALESCE(SUM(CASE WHEN i.product_id = 20 THEN i.amount ELSE 0 END), 0) `teen_packet`,
	COALESCE(SUM(CASE WHEN i.product_id = 25 THEN i.amount ELSE 0 END), 0) `regular_Pads`,
	COALESCE(SUM(CASE WHEN i.product_id = 26 THEN i.amount ELSE 0 END), 0) `overnight_pads`,
	COALESCE(SUM(CASE WHEN i.product_id = 27 THEN i.amount ELSE 0 END), 0) `tampons`,
	COALESCE(SUM(CASE WHEN i.product_id = 28 THEN i.amount ELSE 0 END), 0) `teen_regular_pads`,
	COALESCE(SUM(CASE WHEN i.product_id = 29 THEN i.amount ELSE 0 END), 0) `teen_overnight_pads`,
	COALESCE(SUM(CASE WHEN i.product_id = 30 THEN i.amount ELSE 0 END), 0) `post_partum_pads`,
	COALESCE(SUM(CASE WHEN i.product_id = 31 THEN i.amount ELSE 0 END), 0) `perineal_cold_packs`,
	COALESCE(SUM(CASE WHEN i.product_id = 32 THEN i.amount ELSE 0 END), 0) `my_first_period_packet`
FROM inventory_adjustment a
JOIN inventory i ON i.inventory_adjustment_id = a.id
JOIN product p on p.id = i.product_id

WHERE a.adjustment_type = {$adjustment_type}
AND adjustment_datetime BETWEEN ? AND ?
EOQUERY;

	$detail_query = <<<EOQUERY
SELECT
	adjustment_datetime,
	adjustment_note,
	COALESCE(SUM(CASE WHEN (i.product_id < 10 OR i.product_id = 16) THEN i.amount ELSE 0 END), 0) total_diapers,
	COALESCE(SUM(CASE WHEN i.product_id BETWEEN 10 AND 15 THEN i.amount ELSE 0 END), 0) total_pullups,
	COALESCE(SUM(CASE WHEN i.product_id BETWEEN 17 AND 32 THEN i.amount ELSE 0 END), 0) total_periods,
	COALESCE(SUM(i.amount), 0) total_donated,
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

		return [
			'Aggregate' => DB::select($aggregate_query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59') ]),
			'Detail' => DB::select($detail_query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59') ]),
		];

		return DB::select($query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59') ]);
	}

	public function getTempFilename() {
		return 'purchasereport';
	}

	public function getName($type = 'csv') {
		$name = 'Purchase.Report.' . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [
			'Donations Received',
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

		$aggregate_line = (array) $stats['Aggregate'][0] ?? [];
		$values = [
			$aggregate_line['donations'] ?? 0,
			$aggregate_line['preemie_diapers'] ?? 0,
			$aggregate_line['newborn_diapers'] ?? 0,
			$aggregate_line['size1_diapers'] ?? 0,
			$aggregate_line['size2_diapers'] ?? 0,
			$aggregate_line['size3_diapers'] ?? 0,
			$aggregate_line['size4_diapers'] ?? 0,
			$aggregate_line['size5_diapers'] ?? 0,
			$aggregate_line['size6_diapers'] ?? 0,
			$aggregate_line['size7_diapers'] ?? 0,
			$aggregate_line['other_diapers'] ?? 0,
			$aggregate_line['2t-3t_pullups_boy'] ?? 0,
			$aggregate_line['3t-4t_pullups_boy'] ?? 0,
			$aggregate_line['4t-5t_pullups_boy'] ?? 0,
			$aggregate_line['2t-3t_pullups_girl'] ?? 0,
			$aggregate_line['3t-4t_pullups_girl'] ?? 0,
			$aggregate_line['4t-5t_pullups_girl'] ?? 0,
		];


		$data = array_combine($headers, $values);
		foreach ($data as $key => $value) {
			fputcsv($fp, [ $key, $value ]);
		}
		fclose($fp);
		return $this;
	}

	public function writeheader($fp) {
		fputcsv($fp, ['Report', 'Start Date', 'End Date', 'Generated Date']);
		fputcsv($fp, [
			'Purchase Report',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	protected function getViewData() {
		$diaper_sizes = [
			'preemie' => 'Preemie',
			'newborn' => 'Newborn',
		];
		foreach (range(1, 7) as $sz) {
			$diaper_sizes["size{$sz}"] = "Size {$sz}";
		}
		$diaper_sizes['other'] = 'Other Diapers';

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
		return 'purchases-report';
	}

	protected function isValid() {
		return $this->data && count($this->data) == 2 && count($this->data['Aggregate']) == 1 && count($this->data['Detail']);
	}
}
