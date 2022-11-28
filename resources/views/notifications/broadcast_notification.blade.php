<p>You've received a new notification from Healthy Steps.</p>
<p class="ml2 pl2 bl bw2 b--black-025 pv3 mv2">
	<? if ($isPreview): ?>
		<?= nl2br(e(substr($data['message'], 0, 75))); ?>
		<? if (strlen($data['message']) > 75): ?>
			...<a href="<?= route('notifications.list'); ?>#notification-<?= $Notification->id; ?>">Read more</a>
		<? endif; ?>
	<? else: ?>
		<?= nl2br(e(substr($data['message']))); ?>
	<? endif; ?>
</p>
