<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;

class TestQueue implements ShouldQueue {
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	protected $to;
	protected $subject;
	protected $message;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($to, $subject, $message) {
		$this->to = $to;
		$this->subject = $subject;
		$this->message = $message;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		Mail::raw($this->message, function ($msg) {
			$msg->to($this->to);
			$msg->subject($this->subject);
		});
	}
}
