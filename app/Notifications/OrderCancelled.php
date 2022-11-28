<?php

namespace App\Notifications;

use App\Mail\OrderCancelled as OrderCancelledMail;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderCancelled extends Notification
{
    use Queueable;

    protected $Order;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $Order)
    {
        $this->Order = $Order;
    }

    public function withReason(string $reason = null) {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $Mailable = (new OrderCancelledMail($this->Order))
                ->subject("Order #{$this->Order->full_id} Cancelled")
                ->to($notifiable->routeNotificationFor('mail'));

        if ($this->reason) {
            $Mailable->withReason($this->reason);
        }

        return $Mailable;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->Order->id,
            'reason' => $this->reason,
        ];
    }

    static public function getView() {
        return 'notifications.order_cancelled';
    }

    static public function getViewData($Notification, $data) {
        return [
            'Order' => Order::find($data['order_id']),
        ];
    }
}
