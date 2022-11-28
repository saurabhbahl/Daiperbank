<?php namespace App\Http\Controllers;

use App\Order;
use Auth;
use Illuminate\Http\Request;

class HomeController extends Controller {
	public function __construct() {
		$this->middleware('auth');
	}

	public function index() {
		if (Auth::user()->isAdmin()) {
			return $this->admin_home();
		}

		return $this->agency_home();
	}

	protected function admin_home() {
		return view('admin.home', [
		]);
	}

	protected function agency_home() {
		$Orders = Auth()->User()->Agency->Order()->orderBy('updated_at', 'DESC')->take(5)->get();

		return view('agency.home', [
			'Orders' => $Orders,
		]);
	}

	public function post(Request $Request) {
		if (Auth()->user()->isAdmin()) {
			return $this->post_admin_home($Request);
		}

		return $this->post_agency_home($Request);
	}

	public function post_agency_home(Request $Request) {
		if ($Request->get('action') == 'create') {
			// create a new draft order and start editing it.
			$Order = Auth()->User()->Agency->newOrder()->draft()->create();

			return redirect()->route('order.create', [ $Order->Original ]);
		}

		return redirect()->route('home');
	}
}
