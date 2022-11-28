@component('mail::message')
# Order \#<?= $CancelledOrder->full_id; ?>

Your order has been modified.
<br><br>

Your new Order ID is <?= $NewOrder->full_id; ?>.
<br><br>

You can view the new order by clicking below:
<br><br>

@component('mail::button', ['url' => route('order.view', [ $NewOrder->id ])])
View Order #<?= $NewOrder->full_id; ?>
@endcomponent

<br><br>

If you can not click the button above, copy and paste this URL into your browser:
<br>
    <?= route('order.view', [ $NewOrder->id ]); ?>

<br><br>

If you have further questions regarding this order, please feel free to contact us.
<br><br>

Thank you for your continued support.
<br>
Healthy Steps Diaper Bank
@endcomponent
