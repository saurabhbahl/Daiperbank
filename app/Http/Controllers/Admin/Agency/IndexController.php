<?php namespace App\Http\Controllers\Admin\Agency;

use App\Agency;
use App\AgencySearch;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller {
	public function get(Request $Request) {
		$AgencySearch = new AgencySearch;

		if ($Request->input("filter.search")) {
			$AgencySearch->search($Request->input('filter.search'));
		}

		if ($Request->input('per_page')) {
			$AgencySearch->perPage($Request->input('per_page'));
		}

		if ($Request->input('page')) {
			$AgencySearch->page($Request->input('page'));
		}

		$selected_order_status = $Request->get('status', 'all');
		if ($Request->has('filter')) {
			$selected_order_status = null;
		} else {
			if ($selected_order_status != 'all') {
				$AgencySearch->status($selected_order_status);
			}

			//$this->setOrderSortForStatus($AgencySearch, $selected_order_status);
		}
	//	$selected_order_status='all';
		$Agencies = $AgencySearch->get();

		return view('admin.agency.index', [
			'selected_order_status' => $selected_order_status,
			'Agencies' => $Agencies,
			'Pagination' => $AgencySearch->paginate()->appends($Request->except('page', '_url')),
		]);
	}
protected function setOrderSortForStatus($Search, $status) {
		switch ($status) {
			case 'draft':
				$Search->orderBy('agency.agency_status', 'DESC');
				break;			
			case 'rejected':
				$Search->orderBy('order.updated_at', 'DESC');
				break;

			default:
				$Search->orderBy('agency.id', 'DESC');
				break;
		}

		return $Search;
	}
}