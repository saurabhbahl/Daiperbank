<?php namespace App\Reports;

use App\InventoryAdjustment;
use App\Reports\Exceptions\InsufficientDataException;
use Carbon\Carbon;
use DB;

class InventoryOverview extends Report implements ReportContract {
	use WritesToTempFiles;

	public function getStats() {
		$query = <<<EOQUERY
SELECT * FROM (
	SELECT
		? report_date,
		COALESCE(SUM( IF(product_id = 1, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) preemie_diapers,
		COALESCE(SUM( IF(product_id = 2, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) newborn_diapers,
		COALESCE(SUM( IF(product_id = 3, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size1_diapers,
		COALESCE(SUM( IF(product_id = 4, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size2_diapers,
		COALESCE(SUM( IF(product_id = 5, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size3_diapers,
		COALESCE(SUM( IF(product_id = 6, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size4_diapers,
		COALESCE(SUM( IF(product_id = 7, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size5_diapers,
		COALESCE(SUM( IF(product_id = 8, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size6_diapers,
		COALESCE(SUM( IF(product_id = 9, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size7_diapers,
		COALESCE(SUM( IF(product_id = 16, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) other_diapers,
		COALESCE(SUM( IF(product_id = 10, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `2t-3t_pullups_boy`,
		COALESCE(SUM( IF(product_id = 11, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `3t-4t_pullups_boy`,
		COALESCE(SUM( IF(product_id = 12, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `4t-5t_pullups_boy`,
		COALESCE(SUM( IF(product_id = 13, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `2t-3t_pullups_girl`,
		COALESCE(SUM( IF(product_id = 14, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `3t-4t_pullups_girl`,
		COALESCE(SUM( IF(product_id = 15, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `4t-5t_pullups_girl`,
		COALESCE(SUM( IF(product_id = 25, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `regular_Pads`,
		COALESCE(SUM( IF(product_id = 26, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `overnight_pads`,
		COALESCE(SUM( IF(product_id = 27, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `tampons`,
		COALESCE(SUM( IF(product_id = 28, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `teen_regular_pads`,
		COALESCE(SUM( IF(product_id = 29, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `teen_overnight_pads`,
		COALESCE(SUM( IF(product_id = 30, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `post_partum_pads`,
		COALESCE(SUM( IF(product_id = 31, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `perineal_cold_packs`
	FROM inventory_adjustment a
	JOIN inventory i ON i.inventory_adjustment_id = a.id
	JOIN product p on p.id = i.product_id
	WHERE adjustment_datetime <= ?
	GROUP BY report_date
) q1

UNION ALL

SELECT * FROM (
	SELECT
		? report_date,
		COALESCE(SUM( IF(product_id = 1, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) preemie_diapers,
		COALESCE(SUM( IF(product_id = 2, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) newborn_diapers,
		COALESCE(SUM( IF(product_id = 3, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size1_diapers,
		COALESCE(SUM( IF(product_id = 4, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size2_diapers,
		COALESCE(SUM( IF(product_id = 5, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size3_diapers,
		COALESCE(SUM( IF(product_id = 6, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size4_diapers,
		COALESCE(SUM( IF(product_id = 7, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size5_diapers,
		COALESCE(SUM( IF(product_id = 8, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size6_diapers,
		COALESCE(SUM( IF(product_id = 9, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) size7_diapers,
		COALESCE(SUM( IF(product_id = 16, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) other_diapers,
		COALESCE(SUM( IF(product_id = 10, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `2t-3t_pullups_boy`,
		COALESCE(SUM( IF(product_id = 11, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `3t-4t_pullups_boy`,
		COALESCE(SUM( IF(product_id = 12, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `4t-5t_pullups_boy`,
		COALESCE(SUM( IF(product_id = 13, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `2t-3t_pullups_girl`,
		COALESCE(SUM( IF(product_id = 14, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `3t-4t_pullups_girl`,
		COALESCE(SUM( IF(product_id = 15, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `4t-5t_pullups_girl`,
		COALESCE(SUM( IF(product_id = 25, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `regular_Pads`,
		COALESCE(SUM( IF(product_id = 26, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `overnight_pads`,
		COALESCE(SUM( IF(product_id = 27, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `tampons`,
		COALESCE(SUM( IF(product_id = 28, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `teen_regular_pads`,
		COALESCE(SUM( IF(product_id = 29, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `teen_overnight_pads`,
		COALESCE(SUM( IF(product_id = 30, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `post_partum_pads`,
		COALESCE(SUM( IF(product_id = 31, CASE WHEN i.txn_type = 'CREDIT' THEN amount ELSE (-1 * CAST(amount AS SIGNED)) END, 0) ), 0) `perineal_cold_packs`
	FROM inventory_adjustment a
	JOIN inventory i ON i.inventory_adjustment_id = a.id
	JOIN product p on p.id = i.product_id
	WHERE adjustment_datetime <= ?
	GROUP BY report_date
) q2
EOQUERY;

		$start_date = $this->getStartDate();
		$end_date = $this->getEndDate();

		return DB::select($query, [
			$start_date->format('Y-m-d'),
			$start_date->format('Y-m-d 23:59:59'),
			$end_date->format('Y-m-d'),
			$end_date->format('Y-m-d 23:59:59'),
		]);
	}

	protected function getStartDate() {
		if (InventoryAdjustment::where('adjustment_datetime', '<=', $this->start->format('Y-m-d 23:59:59'))->count() > 0) {
			return $this->start;
		}

		$first_row = InventoryAdjustment::orderBy('adjustment_datetime', 'ASC')
						->whereNull('order_id')
						->take(1)
						->first();

		if ( ! $first_row) {
			throw new InsufficientDataException;
		}

		return carbon($first_row->adjustment_datetime)->setTime(23, 59, 59);
	}

	protected function getEndDate() {
		$ts = min($this->end->format('U'), carbon()->format('U'));

		return Carbon::createFromTimestamp($ts);
	}

	public function getTempFilename() {
		return 'inventoryreport';
	}

	public function getName($type = 'csv') {
		$name = 'Inventory.Report.' . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$data = [
			['Inventory Date'],
			['Preemie Diapers On Hand'],
			['Newborn Diapders On Hand'],
			['Size1 Diapers On-hand'],
			['Size2 Diapers On-hand'],
			['Size3 Diapers On-hand'],
			['Size4 Diapers On-hand'],
			['Size5 Diapers On-hand'],
			['Size6 Diapers On-hand'],
			['Size7 Diapers On-hand'],
			['Other Diapers On-hand'],
			['2t-3t Pullups Boy On-hand'],
			['3t-4t Pullups Boy On-hand'],
			['4t-5t Pullups Boy On-hand'],
			['2t-3t Pullups Girl On-hand'],
			['3t-4t Pullups Girl On-hand'],
			['4t-5t Pullups Girl On-hand'],
		];

		foreach ($stats as $line) {
			$line = (array) $line;
			$line_data = [
				$line['report_date'],
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
			];

			foreach ($line_data as $idx => $value) {
				$data[$idx] [] = $value;
			}
		}

		foreach ($data as $line) {
			fputcsv($fp, $line);
		}

		fclose($fp);
		return $this;
	}

	public function writeheader($fp) {
		fputcsv($fp, ['Report', 'Start Date', 'End Date', 'Generated Date']);
		fputcsv($fp, [
			'Inventory Overview Report',
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

	public function getData() {
		return new class($this->data) {
			public $start;
			public $end;

			public function __construct($data) {
				$this->start = $data[0];
				$this->end = $data[1];
			}
		};
	}

	protected function getViewName() {
		return 'inventory-overview';
	}

	protected function isValid() {
		return $this->data && count($this->data) == 2;
	}
}
