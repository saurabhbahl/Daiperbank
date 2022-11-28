<?php namespace App\Http\Controllers\Admin\Reporting;

use App\Reports\AgencyOverview;
use App\Reports\ChildReport;
use App\Reports\DiaperDriveReport;
use App\Reports\DonationReport;
use App\Reports\Exceptions\InsufficientDataException;
use App\Reports\InventoryOverview;
use App\Reports\LocationOverview;
use App\Reports\MilitaryOverview;
use App\Reports\OrganizationOverview;
use App\Reports\PullUpUsageReport;
use App\Reports\DiaperUsageReport;
use App\Reports\PurchaseReport;
use Illuminate\Http\Request;


class ReportController {
	public function get() {
		return view('admin.reporting.index', [
			'reports' => [
				'agency-report' => 'Agency Overview',
				'child-report' => 'Child Report',
				'diaper-drive-report' => 'Diaper Drive Report',
				'donation-report' => 'Donation Overview',
				'inventory-report' => 'Inventory Report',
				'location-report' => 'Location Overview',
				'purchases-report' => 'Purchases',
				'diaper-usage-report' => 'Diaper Usage',
				'pull-up-usage-report' => 'Pull-Up Usage',
				
			],
		]);
	}

	public function post(Request $Request) {
		$start_date = carbon($Request->get('start-date'));
		$end_date = carbon($Request->get('end-date'));

		switch ($Request->get('report')) {
			case 'org-report':
				$Report = new OrganizationOverview($start_date, $end_date);
				break;

			case 'agency-report':
				$Report = new AgencyOverview($start_date, $end_date);

				if ( ! $Request->input('agency-id')) {
					flash('You must select an Agency to run a report on.')->error();
					return redirect()->back();
				}

				$Report->agency($Request->input('agency-id'));
				break;

			case 'location-report':

				$Report = new LocationOverview($start_date, $end_date);
				if( ! $Request->input('zip-codes') && !$Request->input('all-zips')){
					flash('You must select zip code to run a report on.')->error();
					return redirect()->back();
				}
				elseif( ! $Request->input('zip-codes') && $Request->input('all-zips')){
					$Report->allZipCodes((int)$Request->input('all-zips'));
				}
				else if(  $Request->input('zip-codes') && ! $Request->input('all-zips')){
					$Report->zipCodes(array_map('trim', explode(',', $Request->input('zip-codes'))));

				}
			
				else {
					flash('You must select zip code to run a report on.')->error();
					return redirect()->back();
				}

				break;

			case 'child-report':
				$Report = new ChildReport($start_date, $end_date);
				break;

			case 'military-report':
				$Report = new MilitaryOverview($start_date, $end_date);
				break;

			case 'donation-report':
				$Report = new DonationReport($start_date, $end_date);
				break;

			case 'inventory-report':
				$Report = new InventoryOverview($start_date, $end_date);
				break;

			case 'diaper-drive-report':
				$Report = new DiaperDriveReport($start_date, $end_date);
				break;
			case 'pull-up-usage-report':
				$Report = new PullUpUsageReport($start_date, $end_date);

				if ( ! $Request->input('agency-id')) {
					flash('You must select an Agency to run a report on.')->error();
					return redirect()->back();
				}

				$Report->agency($Request->input('agency-id'));
			break;
			case 'diaper-usage-report':
				$Report = new DiaperUsageReport($start_date, $end_date);

				if ( ! $Request->input('agency-id')) {
					flash('You must select an Agency to run a report on.')->error();
					return redirect()->back();
				}

				$Report->agency($Request->input('agency-id'));
			break;
			case 'purchases-report':
				$Report = new PurchaseReport($start_date, $end_date);
			break;
			default:
				return redirect()->route('admin.reporting');
				break;
		}

		try {
			$Report->run();
			// return $Report->view();
			$fn = $Report->toPdf();
			return response()->download($fn, $Report->getName('pdf'))->deleteFileAfterSend(true);
		} catch (InsufficientDataException $e) {
			flash('There is not enough data for the selected reporting period to generate this report.')->error();
			return redirect()->route('admin.reporting');
		}
		// return response()->download($Report->getFilename(), $Report->getName())->deleteFileAfterSend(true);
	}
}
