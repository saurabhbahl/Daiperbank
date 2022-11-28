<?php namespace App\Reports;

use DB;

class ChildReport extends Report implements ReportContract {
	use WritesToTempFiles;

	public function getStats() {
		$query = <<<EOQUERY
SELECT

	c.name child_identifier,
	c.dob,
	TIMESTAMPDIFF(MONTH, c.dob, NOW()) current_age_months,
	a.name agency_name,

	COALESCE(o_count.lifetime_orders, 0) lifetime_orders,
	COALESCE(p_count.lifetime_diapers, 0) lifetime_diapers,
	COALESCE(p_count.lifetime_pullups, 0) lifetime_pullups,

	COALESCE(o_count.36mo_orders, 0) 36mo_orders,
	COALESCE(p_count.36mo_diapers, 0) 36mo_diapers,
	COALESCE(p_count.36mo_pullups, 0) 36mo_pullups

FROM child c
JOIN guardian g ON g.id = c.guardian_id
JOIN agency a ON a.id = c.agency_id

LEFT JOIN (
	SELECT
		oc.child_id,
		COUNT(*) lifetime_orders,
		SUM(pud.pickup_date >= (c.dob + INTERVAL 36 MONTH)) 36mo_orders
	FROM `order` o
	JOIN order_child oc ON oc.order_id = o.id
	JOIN child c ON c.id = oc.child_id
	JOIN pickup_date pud ON o.pickup_date_id = pud.id
	WHERE o.order_status = 'fulfilled'
	GROUP BY oc.child_id
) o_count ON c.id = o_count.child_id

LEFT JOIN (
	SELECT
		oc.child_id,
		SUM(CASE WHEN p.product_category_id = 1 THEN oi.quantity ELSE 0 END) lifetime_diapers,
		SUM(CASE WHEN p.product_category_id = 2 THEN oi.quantity ELSE 0 END) lifetime_pullups,

		SUM(CASE
			WHEN
				p.product_category_id = 1
				AND pud.pickup_date >= (c.dob + INTERVAL 36 MONTH)
			THEN oi.quantity
			ELSE 0
		END) 36mo_diapers,

		SUM(CASE
			WHEN
				p.product_category_id = 2
				AND pud.pickup_date >= (c.dob + INTERVAL 36 MONTH)
			THEN oi.quantity
			ELSE 0
		END) 36mo_pullups

	FROM `order` o
	JOIN pickup_date pud ON o.pickup_date_id = pud.id
	JOIN order_child oc ON oc.order_id = o.id
	JOIN child c ON c.id = oc.child_id
	JOIN order_item oi ON (oi.order_child_id = oc.id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
	JOIN product p ON p.id = oi.product_id
	WHERE o.order_status = 'fulfilled'
	GROUP BY oc.child_id
) p_count ON p_count.child_id = c.id

WHERE c.deleted_at IS NULL
AND c.dob BETWEEN ? AND ?

GROUP BY c.id
ORDER BY c.dob ASC
EOQUERY;

		return DB::select($query, [
			$this->start->format('Y-m-d 00:00:00'), // child dob window start
			$this->end->format('Y-m-d 23:59:59'), // child dob window end
		]);
	}

	public function getTempFilename() {
		return 'childreport';
	}

	public function getName($type = 'csv') {
		$name = 'Child.Age.Report.' . $this->start->diffInMonths() . '-months.thru.' . $this->end->diffInMonths() . '-months.generated-on.' . carbon()->format('Y.m.d-h.ia');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [
			'Child Identifier',
			'DOB',
			'Current Age (Months)',
			'Agency Name',
			'Lifetime Orders',
			'Lifetime Total Diapers',
			'Lifetime Total Pullups',
			'Total Orders After 36 Months',
			'Total Diapers After 36 Months',
			'Total Pullups After 36 Months',
		];
		fputcsv($fp, $headers);

		foreach ($stats as $line) {
			$line = (array) $line;
			fputcsv($fp, [
				$line['child_identifier'],
				$line['dob'],
				$line['current_age_months'],
				$line['agency_name'],
				$line['lifetime_orders'],
				$line['lifetime_diapers'],
				$line['lifetime_pullups'],
				$line['36mo_orders'],
				$line['36mo_diapers'],
				$line['36mo_pullups'],
			]);
		}
		fclose($fp);
		return $this;
	}

	protected function writeHeader($fp) {
		fputcsv($fp, [ 'Report', 'DOB Start Date', 'DOB End Date', 'Generated Date' ]);
		fputcsv($fp, [
			'Child Age Report',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	protected function getViewName() {
		return 'child-report';
	}

	protected function getOrientation() {
		return 'Landscape';
	}
}
