<?php namespace App\Http\Controllers\Family;

use App\Child;
use App\ChildSearch;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Http\Request;

class IndexController extends BaseController {
	public function get(Request $Request) {
		$ChildSearch = (new ChildSearch)->agency(Auth()->User()->Agency->id);

		if ($Request->input('filter.search')) {
			$ChildSearch->search($Request->input('filter.search'));
		}

		if ($Request->input('filter.guardian')) {
			$ChildSearch->guardian(explode(',', $Request->input('filter.guardian')));
		}

		if ($Request->input('page')) {
			$ChildSearch->page($Request->input('page'));
		}

		return view('family.index', [
			'Children' => $ChildSearch->get(),
			'DraftOrders' => Auth()->User()->Agency->OrderDraft,
			'Guardians' => Auth()->User()->Agency->Guardian()->orderBy('name', 'ASC')->get(),
		]);
	}

	public function archive(Request $Request){
		$agency_id = Auth()->User()->Agency->id;
		if ($Request->input('filter.search')) {
			$search = $Request->input('filter.search');
			$archivedChildren = Child::where('name', 'like', '%' . $search . '%')->whereNull('deleted_at')->where('agency_id', $agency_id)->where('is_menstruator', '=', '0')->orderBy('name', 'asc')->paginate(25);
		}
		else{
			$archivedChildren = Child::whereNull('deleted_at')->where('agency_id', $agency_id)->where('is_menstruator', '=', '0')->orderBy('name', 'asc')->paginate(25);
		}
		// dd($archivedChildren);
		return view('family.index', [
			'Children' => $archivedChildren,
			'DraftOrders' => Auth()->User()->Agency->OrderDraft,
			'Guardians' => Auth()->User()->Agency->Guardian()->orderBy('name', 'ASC')->get(),
		]);
	}

	public function unarchive(Request $Request){
		$agency_id = Auth()->User()->Agency->id;
		if ($Request->input('filter.search')) {
			$search = $Request->input('filter.search');
			$archivedChildren = Child::onlyTrashed()->where('name', 'like', '%' . $search . '%')->where('agency_id', $agency_id)->where('is_menstruator', '=', '0')->orderBy('name', 'asc')->paginate(25);
		}
		else{
			$archivedChildren = Child::onlyTrashed()->where('agency_id', $agency_id)->where('is_menstruator', '=', '0')->orderBy('name', 'asc')->paginate(25);
		}
		
		// dd($archivedChildren);
		return view('family.unarchive', [
			'Children' => $archivedChildren,
			'DraftOrders' => Auth()->User()->Agency->OrderDraft,
			'Guardians' => Auth()->User()->Agency->Guardian()->orderBy('name', 'ASC')->get(),
		]);
	}
}
