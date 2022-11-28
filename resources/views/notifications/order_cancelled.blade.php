<p><a href="<?= route('order.view', [ $Order ]); ?>">Order #<?= e($Order->full_id); ?></a> has been cancelled.</p>
<? if ($data['reason']): ?>
	<p>
		<blockquote class="f3">
			<strong>Reason:</strong>
			<br>
			<?= nl2br(e($data['reason'])); ?>
		</blockquote>
	</p>
<? endif; ?>