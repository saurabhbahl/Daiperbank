<?php

namespace App\Mail;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderModified extends Mailable
{
    use Queueable, SerializesModels;

    protected $CancelledOrder;
    protected $NewOrder;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $CancelledOrder, Order $NewOrder)
    {
        $this->CancelledOrder = $CancelledOrder;
        $this->NewOrder = $NewOrder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.orders.modified')
                ->with([
                    'CancelledOrder' => $this->CancelledOrder,
                    'NewOrder' => $this->NewOrder,
                ]);
    }
}
