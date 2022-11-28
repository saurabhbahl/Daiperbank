<?php namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BroadcastNotification extends Mailable
{
    use Queueable, SerializesModels;

    protected $message;
    protected $Recipient;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Recipient, $message)
    {
        $this->Recipient = $Recipient;
        $this->message = $message;
    }

    public function build()
    {
        return $this->markdown('emails.broadcast_notification')->with([
            'message' => $this->message,
        ]);
    }
}
