@component('mail::message')
# Order \#<?= $Order->full_id; ?>

Your order can not be accepted at this time.
<br>

<? if ($reason): ?>
The following reason was given:

<?= implode("\n", array_map(function($line) {
	return "> " . e($line);
}, explode("\n", $reason))); ?>

<br><br>
<? endif; ?>

If you have further questions regarding your order, please feel free to contact us.
<br><br>

Thank you for your continued support.
<br>
Healthy Steps Diaper Bank
@endcomponent
