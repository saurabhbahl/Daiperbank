@component('mail::message')
# Order \#<?= $Order->full_id; ?>

Your order has been approved and will be ready for pickup on:
<br>

**<?= $Order->pickup_on->format('l, F jS, Y') . ' at ' . $Order->pickup_on->format('g:ia'); ?>**

@component('mail::button', ['url' => route('order.view', [ $Order->id ]) ])
View Order
@endcomponent

If you have further questions regarding your order, please feel free to contact us.
<br><br>

Thank you for your continued support,
<br>
Healthy Steps Diaper Bank
@endcomponent
