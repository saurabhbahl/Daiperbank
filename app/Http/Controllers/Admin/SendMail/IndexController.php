<?php namespace App\Http\Controllers\Admin\SendMail;

use App\SendMail;
use App\Agency;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Mail\AgencyMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
class IndexController extends Controller {
	public function get(Request $Request) {
        $Agencies=Agency::get();
        // dd($Agencies);
		$errors = $Request->session()->get('errors', collect());
		return view('admin.send-mail.index', [
			'Agencies' => $Agencies,
			'errors' => $errors,
		]);
	}
	
	public function send_mail(Request $Request) {
		// dd($Request);
		$validator = Validator::make($Request->all(), [
			'a_mail' => 'required|array|min:1', // Ensure a_mail is required and is an array with at least one element
			'subject' => 'required|string', // Ensure subject is required and is a string
		], [
			'a_mail.required' => 'Please select at least one recipient.', // Custom error message for a_mail required rule
			'a_mail.array' => 'The recipients must be provided as an array.', // Custom error message for a_mail array rule
			'a_mail.min' => 'Please select at least one recipient.', // Custom error message for a_mail min rule
			'subject.required' => 'The subject field is required.', // Custom error message for subject required rule
			'subject.string' => 'The subject must be a string.', // Custom error message for subject string rule
		]
		);
	
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput($Request->all());
		}
		$path = base_path().'/img/logo.png';
		$updatedMessage='<p><img style="    text-align: center;" src="'.$path.'" style="height: 100px; width:100px;"></p>'.$Request->message;


		foreach ($Request->a_mail as $recipient) {
			Mail::to($recipient)->send(new AgencyMail($updatedMessage, $Request->subject));
		}
		return redirect()->route('admin.getmail.index')->with('success', 'Mail sent successfully.');
        // dd($Request->all());
	}
}
