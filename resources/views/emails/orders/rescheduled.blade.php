@component('mail::message')
# Order \#<?= $Order->full_id; ?>

Your order has been rescheduled.


Your new pickup date is scheduled for <?= $Order->pickup_on->format('l, F jS, Y'); ?> at <?= $Order->pickup_on->format('g:i A'); ?>


<? if (!empty($reason)): ?>
Your order had to be rescheduled because:
<?= implode("\n", array_map(function($line) {
	return "> " . e($line);
}, explode("\n", $reason))); ?>
<? endif; ?>



You can view your order details by clicking below:
<br>

@component('mail::button', ['url' => route('order.view', [ $Order->id ])])
View Order #<?= $Order->full_id; ?>
@endcomponent

<br><br>

If you can not click the button above, copy and paste this URL into your browser:
<br>
    <?= route('order.view', [ $Order->id ]); ?>

<br><br>

If you have further questions regarding this order, please feel free to contact us.
<br><br>

Thank you for your continued support.
<br>
Healthy Steps Diaper Bank
@endcomponent
