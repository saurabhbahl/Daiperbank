<p><a href="<?= route('order.view', [ $Order ]); ?>">Order #<?= e($Order->full_id); ?></a> has been rejected.</p>
<? if ($data['reason']): ?>
	<p>
		<strong>Reason:</strong>
		<br>
		<?= nl2br(e($data['reason'])); ?>
	</p>
<? endif; ?>