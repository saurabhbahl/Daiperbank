<?php namespace App\Http\Controllers\Admin\SendMail;

use App\SendMail;
use App\Agency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\AgencyMail;
use Illuminate\Support\Facades\Mail;
class IndexController extends Controller {
	public function get(Request $Request) {
        $Agencies=Agency::get();
        // dd($Agencies);
		return view('admin.send-mail.index', [
			'Agencies' => $Agencies,
		]);
	}
	public function send_mail(Request $Request) {
		// dd($Request->a_mail);
		// Mail::to('slowmanfromdhilwan@gmail.com')->send(new AgencyMail($Request->message));
		foreach ($Request->a_mail as $recipient) {
			Mail::to($recipient)->send(new AgencyMail($Request->message));
		}
		return redirect()->route('admin.getmail.index');
        // dd($Request->all());
	}
}
