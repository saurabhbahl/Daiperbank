<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCancelled extends Mailable
{
    use Queueable, SerializesModels;

    protected $Order;
    protected $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $Order)
    {
        $this->Order = $Order;
    }

    public function withReason($reason) {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orders.cancelled')->with([
            'Order' => $this->Order,
            'reason' => $this->reason,
        ]);
    }
}
