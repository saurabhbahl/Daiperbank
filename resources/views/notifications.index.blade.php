
@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Notifications</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">

			<div class="fg fs oy-auto">
				<? foreach ($AllNotifications as $Notification): ?>
					<div id="notification-<?= $Notification->id; ?>">
						<?= $Notification->render($preview = false); ?>
					</div>
				<? endforeach; ?>
			</div>
		</div>
	</div>
</