<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderRescheduled extends Mailable
{
    use Queueable, SerializesModels;

    protected $Order;
    protected $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $Order, $reason = null)
    {
        $this->Order = $Order;
        $this->reason = $reason;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orders.rescheduled')
                ->with([
                    'Order' => $this->Order,
                    'reason' => $this->reason,
                ]);
    }
}
