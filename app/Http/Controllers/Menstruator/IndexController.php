<?php namespace App\Http\Controllers\Menstruator;

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

		return view('Menstruator.index', [
			'Children' => $ChildSearch->getMenstruator(),
			'DraftOrders' => Auth()->User()->Agency->OrderDraft,
			'Guardians' => Auth()->User()->Agency->Guardian()->orderBy('name', 'ASC')->get(),
		]);
	}
}
