<? if (Auth()->User()->Agency->isAdmin()): $link = route('admin.order.view', [ $Order ]); ?>
<? else: $link = route('order.view', [ $Order ]); ?>
<? endif; ?>

<?php if ($Note): ?>
	<p>
		<?= e($Note->Author->Agency->name); ?> has made a new comment on <a href="<?= $link; ?>">Order #<?= e($Order->full_id); ?>.</a>
	</p>
<? else: ?>
	<p>
		<em>A comment was created for order <a href="<?= $link; ?>">#<?= $Order->id; ?></a>, but has since been deleted.</em>
	</p>
<? endif; ?>