<?php namespace App\Reports;

use App\Agency;
use DB;

class DiaperUsageReport extends Report implements ReportContract {
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
		c.name as name,
		o.Id as ordernumber,
		p.name as productname,
		pc.name as productcategory,
		pud.pickup_date as pickupdate
	FROM child c
	LEFT JOIN order_child oc ON oc.child_id = c.id
	LEFT JOIN `order` o ON o.id = oc.order_id
	LEFT JOIN order_item oi ON (oi.order_child_id = oc.id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
	LEFT JOIN pickup_date pud ON pud.id = o.pickup_date_id
	LEFT JOIN product p ON p.id = oi.product_id
	LEFT JOIN product_category pc on pc.id=p.product_category_id
	LEFT JOIN agency a on a.id=o.agency_id

	WHERE pc.id=1
	AND pud.pickup_date BETWEEN ? AND ?
	AND a.id = ?

	ORDER BY c.name ASC
EOQUERY;

		return DB::select($query, [ $this->start->format('Y-m-d 00:00:00'), $this->end->format('Y-m-d 23:59:59'), $this->agency_id ]);
	}

	public function getTempFilename() {
		return 'agencyoverview';
	}

	public function getName($type = 'csv') {
		$Agency = $this->getAgency();

		$name = "Diaper.Usage.{$Agency->id_prefix}-{$Agency->id}." . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [ 'Report Start Date', 'Report End Date', 'Name', 'Order Number', 'Product Name', 'Product Category', 'Pickup Date' ];
		fputcsv($fp, $headers);
		foreach ($stats as $line) {
			$line = (array) $line;
			fputcsv($fp, [
				$this->start->format('Y-m-d'),
				$this->end->format('Y-m-d'),
				$line['name'],
				$line['ordernumber'],
				$line['productname'],
				$line['productcategory'],
				$line['pickupdate'],
			]);
		}
		fclose($fp);
		return $this;
	}

	public function writeheader($fp) {
		fputcsv($fp, ['Report', 'Start Date', 'End Date', 'Generated Date']);
		fputcsv($fp, [
			'Diaper Usage',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	protected function getViewName() {
		return 'diaper-usage-report';
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
