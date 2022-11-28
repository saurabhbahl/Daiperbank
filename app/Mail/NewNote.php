<?php namespace App\Mail;

use App\Note;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewNote extends Mailable
{
    use Queueable, SerializesModels;

    protected $Recipient;
    protected $Model;
    protected $Note;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($Recipient, Model $Model, Note $Note)
    {
        $this->Recipient = $Recipient;
        $this->Model = $Model;
        $this->Note = $Note;
    }

    public function build()
    {
        return $this->markdown('emails.notes.created')->with([
            'Recipient' => $this->Recipient,
            'Model' => $this->Model,
            'Note' => $this->Note,
        ]);
    }
}
