<?php

namespace App\Notifications;

use App\Mail\OrderRescheduled as OrderRescheduledMail;
use App\Order;
use App\PickupDate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderRescheduled extends Notification
{
    use Queueable;

    protected $Order;
    protected $OldPickupDate;
    protected $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $Order, PickupDate $OldPickupDate, $reason = null)
    {
        $this->Order = $Order;
        $this->OldPickupDate = $OldPickupDate;
        $this->reason = $reason;
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
        $Mailable = (new OrderRescheduledMail($this->Order, $this->reason))
                ->subject("Order #{$this->Order->full_id} Rescheduled")
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
            'old_pickup_date_id' => $this->OldPickupDate->id,
            'reason' => $this->reason,
        ];
    }

    static public function getView() {
        return 'notifications.order_rescheduled';
    }

    static public function getViewData($Notification, $data) {
        return [
            'Order' => Order::find($data['order_id']),
            'OldPickupDate' => (!empty($data['old_pickup_date'])? PickupDate::find($data['old_pickup_date_id']) : null),
        ];
    }
}
