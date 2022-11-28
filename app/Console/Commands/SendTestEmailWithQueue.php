<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SendTestEmailWithQueue extends Command {
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'hsdb:send-test-email-with-queue
                            {--subject= : Subject for the email [optional, default=Test + Timestamp + Random String]}
                            {--message= : Message for the email [optiona, default=same as subject}
                            {to : Email address for the recipient of this message [required]}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Dispatch a "TestQueue" job on the queue pipeline to ensure queues are being properly processed.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$subject = $this->option('subject');
		if (empty($subject)) {
			$subject = 'Test ' . carbon()->format('F j, Y @ H:i') . ' ' . Str::random(8);
		}

		$message = $this->option('message');
		if (empty($message)) {
			$message = $subject;
		}

		$to = $this->argument('to');

		dispatch(new \App\Jobs\TestQueue($to, $subject, $message));

		$this->info("Message queued.\nTo: {$to}\nSubject: {$subject}\nMessage: {$message}");
	}
}
