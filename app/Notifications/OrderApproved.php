<?php

namespace App\Notifications;

use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\OrderApproved as OrderApprovedMail;

class OrderApproved extends Notification
{
    use Queueable;

    protected $Order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $Order)
    {
        $this->Order = $Order;
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
        $Mailable = (new OrderApprovedMail($this->Order))
                ->subject("Order #{$this->Order->full_id} Approved")
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
            'order_id' => $this->Order->id,
        ];
    }

    static public function getView() {
        return 'notifications.order_approved';
    }

    static public function getViewData($Notification, $data) {
        return [
            'Order' => Order::find($data['order_id']),
        ];
    }
}
