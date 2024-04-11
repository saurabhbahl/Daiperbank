<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AgencyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message, $subject)
    {
        $this->message = $message;
        $this->subject = $subject; // Set the subject
    }

    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
    {
        return $this->subject($this->subject) // Set the subject here
                    ->markdown('emails.sendagency')
                    ->with([
                        'Message' => $this->message,
                    ]);
    }
}
