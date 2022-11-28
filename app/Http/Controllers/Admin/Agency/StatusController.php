<?php namespace App\Http\Controllers\Admin\Agency;

use App\Agency;
use App\Http\Controllers\Controller as BaseController;
use DB;
use Illuminate\Http\Request;

class StatusController extends BaseController {
	public function post(Request $Request, Agency $Agency) {
		$this->validate($Request, $this->rules(), $this->messages());
        
		DB::transaction(function() use ($Request, $Agency) {{

			$Agency->update([
				'agency_status' => $Request->get('agency_status'),
			]);

			if ($Agency->isActive()) {
				$Agency->User->activate();
			} else {
				$Agency->User->deactivate();
			}
		}});

		flash("Agency status updated successfully")->success();
		return redirect()->back();
	}

	protected function rules() {
		return [
			'agency_status' => ['required', 'in:active,inactive'],
		];
	}

	protected function messages() {
		return [
		];
	}
}