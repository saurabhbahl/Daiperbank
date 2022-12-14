<?php namespace App\Reports;

use DB;
use Illuminate\Support\Str;
use mikehaertl\pdftk\Pdf as PDF;

class LocationOverviewAll extends Report implements ReportContract {
	use WritesToTempFiles;
	protected $zips = [];
	
	public function zipCodes(array $zips) {
		$this->zips = $zips;
	}

	public function getStats() {
		$query = <<<EOQUERY
SELECT
	c.zip,
	z.city,
	z.state_abbr,
	z.county,
	SUM(CASE WHEN p.product_category_id = 1 THEN quantity ELSE 0 END) diapers,
	SUM(CASE WHEN p.product_category_id = 2 THEN quantity ELSE 0 END) pull_ups,
	COUNT(DISTINCT c.id) children,
	COUNT(DISTINCT c.guardian_id) families,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE "non-military%" THEN c.id END) military_children,
	COUNT(DISTINCT CASE WHEN LOWER(g.military_status) NOT LIKE "non-military%" THEN g.id END) military_families

FROM `order` o
JOIN pickup_date pud ON pud.id = o.pickup_date_id
JOIN order_child oc ON oc.order_id = o.id
JOIN order_item oi ON (oi.order_child_id = oc.id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
JOIN child c ON (c.id = oc.child_id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
JOIN guardian g ON g.id = c.guardian_id
JOIN product p ON p.id = oi.product_id
JOIN zipcode z ON z.zip = c.zip

WHERE o.order_status = 'fulfilled'
AND oi.deleted_at IS NULL
and oi.flag_approved = 1
AND pud.pickup_date BETWEEN ? AND ?


GROUP BY c.zip, z.state_abbr, z.county, z.city
ORDER BY c.zip, z.state_abbr, z.county, z.city ASC
EOQUERY;

		$aggregate_stats = DB::select($query, array_merge([
			$this->start->format('Y-m-d 00:00:00'),
			$this->end->format('Y-m-d 23:59:59'),
		]));

		$children = $this->getChildrenServed();

		$child_data = array_reduce($children, function ($aggregate, $Child) {
			if ( ! isset($aggregate[$Child->zip])) {
				$aggregate[$Child->zip] = [];
			}

			$zip_agg = $aggregate[$Child->zip];

			if ( ! isset($zip_agg[$Child->race])) {
				$zip_agg[$Child->race] = 0;
			}

			if ( ! isset($zip_agg[$Child->ethnicity])) {
				$zip_agg[$Child->ethnicity] = 0;
			}

			$zip_agg[$Child->race]++;
			$zip_agg[$Child->ethnicity]++;

			$aggregate[$Child->zip] = $zip_agg;

			return $aggregate;
		}, []);

		$this->data = array_map(function ($stat) use ($child_data) {
			if (isset($child_data[$stat->zip])) {
				return (object) array_merge((array) $stat, $child_data[$stat->zip]);
			}

			return $stat;
		}, $aggregate_stats);

		return $this->data;
	}

	public function prepareQuery($query) {
		$zip_count = count($this->zips);
		$param_bindings = array_fill(0, $zip_count, '?');

		return str_replace('{{ ZIP_CODE_PARAM_PLACEHOLDER }}', implode(',', $param_bindings), $query);
	}

	protected function getChildrenServed() {
		$query = <<<EOQUERY
SELECT
	c.*
FROM `order` o
JOIN pickup_date pud ON pud.id = o.pickup_date_id
JOIN order_child oc ON oc.order_id = o.id
JOIN order_item oi ON (oi.order_child_id = oc.id AND oi.deleted_at IS NULL AND oi.flag_approved = 1)
JOIN child c ON c.id = oc.child_id

WHERE o.order_status = 'fulfilled'
AND pud.pickup_date BETWEEN ? AND ?
GROUP BY c.id
EOQUERY;

		return DB::select($query, array_merge([
			$this->start->format('Y-m-d 00:00:00'),
			$this->end->format('Y-m-d 23:59:59'),
		]));
	}

	public function getTempFilename() {
		return 'locationoverview';
	}

	public function getName($type = 'csv') {
		$name = 'Location.Overview.' . $this->start->format('Y-m-d') . '.thru.' . $this->end->format('Y-m-d');

		return "{$name}.{$type}";
	}

	public function writeCsv(array $stats, $filename) {
		$fp = fopen($filename, 'w');

		$this->writeHeader($fp);

		$headers = [
			'Zip',
			'State',
			'County',
			'City',
			'Diapers Distributed',
			'Pullups Distributed',
			'Children Served',
			'Families Served',
			'Military Children Served',
			'Military Families Served',
			'Hispanic Or Latino',
			'Non-hispanic Or Latino',
			'American Indian Or Alaska Native',
			'Asian',
			'Black Or African American',
			'Native Hawaiian Or Pacific Islander',
			'White',
			'Two Or More Races',
			'Other',
		];
		fputcsv($fp, $headers);
		foreach ($stats as $line) {
			$line = (array) $line;
			fputcsv($fp, [
				$line['zip'],
				$line['state_abbr'],
				$line['county'],
				$line['city'],
				$line['diapers'],
				$line['pull_ups'],
				$line['children'],
				$line['families'],
				$line['military_children'],
				$line['military_families'],
				$line['Hispanic or Latino'] ?? 0,
				$line['Non-Hispanic or Latino'] ?? 0,
				$line['American Indian or Alaska Native'] ?? 0,
				$line['Asian'] ?? 0,
				$line['Black or African American'] ?? 0,
				$line['Native Hawaiian or Pacific Islander'] ?? 0,
				$line['White'] ?? 0,
				$line['Two or More Races'] ?? 0,
				$line['Other'] ?? 0,
			]);
		}
		fclose($fp);
		return $this;
	}

	public function writeheader($fp) {
		fputcsv($fp, ['Report', 'Start Date', 'End Date', 'Generated Date']);
		fputcsv($fp, [
			'Location Overview Report',
			$this->start->format('Y-m-d'),
			$this->end->format('Y-m-d'),
			carbon()->format('Y-m-d @ h:i a'),
		]);

		fputcsv($fp, [' ']);

		return $this;
	}

	protected function getViewName() {
		return 'location-overview';
	}

	public function getData() {
		return $this->stats[0];
	}

	public function toPdf($final_pdf_fn = null) {
		$pdfs = [];
		foreach ($this->data as $zip_data) {
			$LocationReport = new LocationSubReport($this->start, $this->end);
			$LocationReport->setData($zip_data);

			$pdfs [] = $LocationReport->toPdf($this->getTempFilename() . ".{$zip_data->zip}." . Str::random());
		}

		$FinalPDF = array_reduce($pdfs, function ($PDF, $pdf_fn) {
			return $PDF->addFile($pdf_fn);
		}, new PDF);

		if ( ! $final_pdf_fn) {
			$final_pdf_fn = $this->makeTempFile($this->getTempFilename() . '.combined.' . Str::random());
		}
		$FinalPDF->saveAs($final_pdf_fn);

		array_map(function ($fn) {
			unlink($fn);
		}, $pdfs);

		return $final_pdf_fn;
	}
}
