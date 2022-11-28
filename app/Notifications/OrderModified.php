<?php

namespace App\Notifications;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\OrderModified as OrderModifiedMail;

class OrderModified extends Notification
{
    use Queueable;

    protected $Cancelled;
    protected $New;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $Cancelled, Order $New)
    {
        $this->CancelledOrder = $Cancelled;
        $this->NewOrder = $New;
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
        $Mailable = (new OrderModifiedMail($this->CancelledOrder, $this->NewOrder))
                ->subject("Order #{$this->Order->full_id} Modified")
                ->to($notifiable->routeNotificationFor('mail'));

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
            'new_order_id' => $this->New->id,
            'old_order_id' => $this->Cancelled->id,
        ];
    }

    static public function getView() {
        return 'notifications.order_modified';
    }

    static public function getViewData($Notification, $data) {
        return [
            'Order' => Order::find($data['order_id']),
        ];
    }
}
