<?php namespace App\Reports;

use App\Agency;
use DB;

class AgencyOverview extends Report implements ReportContract {
	use WritesToTempFiles;
	protected $agency_id;

	public function agency($id) {
		$this->agency_id = $id;
	}

	public function getAgency() {
		return Agency::find($this->agency_id);
	}

	public function getStats() {
		$query = <<<EOQUERY
SELECT
	a.name agency,
	COALESCE(SUM(CASE WHEN p.product_category_id = 1 THEN quantity ELSE 0 END), 0) diapers,
	COALESCE(SUM(CASE WHEN p.product_category_id = 2 THEN quantity ELSE 0 END), 0) pull_ups,
	COALESCE(COUNT(DISTINCT oc.child_id), 0) children,
	COALESCE(COUNT(DISTINCT c.guardian_id), 0) families

FROM agency a
LEFT JOIN `order` o ON a.id = o.agency_id
LEFT JOIN pickup_date pud ON pud.id = o.pickup_date_id
LEFT JOIN order_child oc ON oc.order_id = o.id
LEFT JOIN order_item oi ON (oi.order_child_id = oc.id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
LEFT JOIN child c ON c.id = oc.child_id
LEFT JOIN product p ON p.id = oi.product_id

WHERE o.order_status = 'fulfilled'
AND pud.pickup_date BETWEEN ? AND ?
AND a.id = ?

GROUP BY a.id
ORDER BY a.name ASC
EOQUERY;

		return DB::select($query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59'), $this->agency_id ]);
	}

	public function getTempFilename() {
		return 'agencyoverview';
	}

	public function getName($type = 'csv') {
		$Agency = $this->getAgency();

		$name = "Agency.Overview.{$Agency->id_prefix}-{$Agency->id}." . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [ 'Report Start Date', 'Report End Date', 'Agency', 'Diapers Distributed', 'Pullups Distributed', 'Children Served', 'Families Served' ];
		fputcsv($fp, $headers);
		foreach ($stats as $line) {
			$line = (array) $line;
			fputcsv($fp, [
				$this->start->format('Y-m-d'),
				$this->end->format('Y-m-d'),
				$line['agency'],
				$line['diapers'],
				$line['pull_ups'],
				$line['children'],
				$line['families'],
			]);
		}
		fclose($fp);
		return $this;
	}

	public function writeheader($fp) {
		fputcsv($fp, ['Report', 'Start Date', 'End Date', 'Generated Date']);
		fputcsv($fp, [
			'Agency Overview',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	protected function getViewName() {
		return 'agency-overview';
	}

	protected function getViewData() {
		return array_merge(parent::getViewData(), [
			'stats' => $this->data[0],
			'Agency' => $this->getAgency(),
		]);
	}

	protected function isValid() {
		return $this->data && count($this->data) == 1;
	}
}
