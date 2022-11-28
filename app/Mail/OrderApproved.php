<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderApproved extends Mailable
{
    use Queueable, SerializesModels;

    protected $Order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $Order)
    {
        $this->Order = $Order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orders.approved')->with([
            'Order' => $this->Order,
        ]);
    }
}
