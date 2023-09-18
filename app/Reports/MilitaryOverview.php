<?php namespace App\Reports;

use DB;

class MilitaryOverview extends Report implements ReportContract {
	use WritesToTempFiles;

	public function getStats() {
		$query = <<<EOQUERY
SELECT
	SUM(CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND p.product_category_id = 1 THEN quantity ELSE 0 END) military_diapers,
	SUM(CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND p.product_category_id = 2 THEN quantity ELSE 0 END) military_pull_ups,
	SUM(CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND p.product_category_id = 3 THEN quantity ELSE 0 END) military_period_products,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND c.is_menstruator = 0 THEN c.id END) military_children,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' AND c.is_menstruator = 1 THEN c.id END) military_mens,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE 'non-military%' THEN g.id END) military_families

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
		$name = 'Military.Overview.' . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	protected function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [
			'Military Diapers Distributed',
			'Military Pullups Distributed',
			'Military Children Served',
			'Military Families Served',
		];

		$line = (array) $stats[0];
		$values = [
			$line['military_diapers'],
			$line['military_pull_ups'],
			$line['military_children'],
			$line['military_families'],
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
			'Military Overview',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	public function getData() {
		return $this->data[0];
	}

	protected function getViewName() {
		return 'military-overview';
	}

	protected function isValid() {
		return $this->data && count($this->data) == 1;
	}
}
