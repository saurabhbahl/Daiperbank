<?php namespace App\Reports;

use DB;

class OrganizationOverview extends Report implements ReportContract {
	use WritesToTempFiles;

	public function getStats() {
		$query = <<<EOQUERY
SELECT
	SUM(CASE WHEN p.product_category_id = 1 THEN quantity ELSE 0 END) diapers,
	SUM(CASE WHEN p.product_category_id = 2 THEN quantity ELSE 0 END) pull_ups,
	SUM(CASE WHEN oi.product_id BETWEEN 17 AND 32 THEN quantity ELSE 0 END) period_products,
	COUNT(DISTINCT CASE WHEN c.is_menstruator = 0 THEN c.id END) child,
    COUNT(DISTINCT CASE WHEN c.is_menstruator = 1 THEN c.id END) menstruators,
	COUNT(DISTINCT c.guardian_id) families,
	COUNT(DISTINCT o.id) orders,
	COUNT(DISTINCT o.agency_id) agencies,
	SUM(CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND p.product_category_id = 1 THEN quantity ELSE 0 END) military_diapers,
	SUM(CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND p.product_category_id = 2 THEN quantity ELSE 0 END) military_pull_ups,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND c.is_menstruator = 0 THEN c.id END) military_children,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND c.is_menstruator = 1 THEN c.id END) military_mens,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' THEN g.id END) military_families,
	COALESCE(SUM(CASE WHEN oi.product_id = 1 THEN oi.quantity ELSE 0 END), 0) preemie_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 2 THEN oi.quantity ELSE 0 END), 0) newborn_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 3 THEN oi.quantity ELSE 0 END), 0) size1_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 4 THEN oi.quantity ELSE 0 END), 0) size2_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 5 THEN oi.quantity ELSE 0 END), 0) size3_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 6 THEN oi.quantity ELSE 0 END), 0) size4_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 7 THEN oi.quantity ELSE 0 END), 0) size5_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 8 THEN oi.quantity ELSE 0 END), 0) size6_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 9 THEN oi.quantity ELSE 0 END), 0) size7_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 16 THEN oi.quantity ELSE 0 END), 0) other_diapers,
	COALESCE(SUM(CASE WHEN oi.product_id = 10 THEN oi.quantity ELSE 0 END), 0) `2t-3t_pullups_boy`,
	COALESCE(SUM(CASE WHEN oi.product_id = 11 THEN oi.quantity ELSE 0 END), 0) `3t-4t_pullups_boy`,
	COALESCE(SUM(CASE WHEN oi.product_id = 12 THEN oi.quantity ELSE 0 END), 0) `4t-5t_pullups_boy`,
	COALESCE(SUM(CASE WHEN oi.product_id = 13 THEN oi.quantity ELSE 0 END), 0) `2t-3t_pullups_girl`,
	COALESCE(SUM(CASE WHEN oi.product_id = 14 THEN oi.quantity ELSE 0 END), 0) `3t-4t_pullups_girl`,
	COALESCE(SUM(CASE WHEN oi.product_id = 15 THEN oi.quantity ELSE 0 END), 0) `4t-5t_pullups_girl`,
	COALESCE(SUM(CASE WHEN oi.product_id = 17 THEN oi.quantity ELSE 0 END), 0) `pads_only_packet`,
	COALESCE(SUM(CASE WHEN oi.product_id = 18 THEN oi.quantity ELSE 0 END), 0) `tampons_and_pads_packet`,
	COALESCE(SUM(CASE WHEN oi.product_id = 19 THEN oi.quantity ELSE 0 END), 0) `postpartum_packet`,
	COALESCE(SUM(CASE WHEN oi.product_id = 20 THEN oi.quantity ELSE 0 END), 0) `teen_packet`,
	COALESCE(SUM(CASE WHEN oi.product_id = 32 THEN oi.quantity ELSE 0 END), 0) `my_first_period_packet`

FROM `order` o
JOIN pickup_date pud ON pud.id = o.pickup_date_id
JOIN order_child oc ON oc.order_id = o.id
JOIN order_item oi ON (oi.order_child_id = oc.id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
JOIN child c ON c.id = oc.child_id
JOIN product p ON p.id = oi.product_id
JOIN guardian g ON g.id = c.guardian_id

WHERE o.order_status = 'fulfilled'
AND pud.pickup_date BETWEEN ? AND ?
EOQUERY;

		return DB::select($query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59') ]);
	}

	public function getTempFilename() {
		return 'orderoverview';
	}

	public function getName($type = 'csv') {
		$name = 'Organization.Overview.' . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	protected function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [
			'Diapers Distributed',
			'Pullups Distributed',
			'Children Served',
			'Families Served',
			'Military Diapers Distributed',
			'Military Pullups Distributed',
			'Military Children Served',
			'Military Families Served',
			'Total Preemie Diapers Distributed',
			'Total Newborn Diapers Distributed',
			'Total Size1 Diapers Distributed',
			'Total Size2 Diapers Distributed',
			'Total Size3 Diapers Distributed',
			'Total Size4 Diapers Distributed',
			'Total Size5 Diapers Distributed',
			'Total Size6 Diapers Distributed',
			'Total Size7 Diapers Distributed',
			'Total Other Diapers Distributed',
			'Total 2t-3t Pullups Boy Distributed',
			'Total 3t-4t Pullups Boy Distributed',
			'Total 4t-5t Pullups Boy Distributed',
			'Total 2t-3t Pullups Girl Distributed',
			'Total 3t-4t Pullups Girl Distributed',
			'Total 4t-5t Pullups Girl Distributed',
		];

		$line = (array) $stats[0];
		$values = [
			$line['diapers'],
			$line['pull_ups'],
			$line['children'],
			$line['families'],
			$line['military_diapers'],
			$line['military_pull_ups'],
			$line['military_children'],
			$line['military_families'],
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
			'Organization Overview',
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
		return $this->data[0];
	}

	protected function getViewName() {
		return 'organization-overview';
	}

	protected function isValid() {
		return $this->data && count($this->data) == 1;
	}
}
