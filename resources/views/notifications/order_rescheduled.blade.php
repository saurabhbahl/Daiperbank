<p>
	<a href="<?= route('order.view', [ $Order ]); ?>">Order #<?= e($Order->full_id); ?></a> has been rescheduled.
	<br>
	Your new pickup date is scheduled for <?= $Order->pickup_on->format('l, F jS, Y @ g:ia'); ?>.
</p>