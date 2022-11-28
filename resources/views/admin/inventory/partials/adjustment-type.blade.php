<span class="txn-type txn-type--<?= $Adjustment->adjustment_type; ?>">
	<? if ($Adjustment->isFromOrder()): ?>
		<a href="<?= route('admin.order.view', [$Adjustment->order_id]); ?>">
	<? endif; ?>

	<?= $Adjustment->detail_string; ?>

	<? if ($Adjustment->isFromOrder()): ?>
		</a>
	<? endif; ?>
</span>