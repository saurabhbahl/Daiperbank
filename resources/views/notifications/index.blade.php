
@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Notifications</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">

			<div class="fg fs">
				<? if ($AllNotifications->count()): ?>
					<? foreach ($AllNotifications as $Notification): ?>
						<div class="pa4 bb b--black-20">
							<div class="muted f4 i">
								<? if ( ! $Notification->read_at): ?>
									<i class="fa fa-bell dark-red"></i>
								<? endif; ?>

								Posted <?= $Notification->humanReadablePostedTime(); ?>
							</div>

							<div class="f3">
								<?= $Notification->render(); ?>
							</div>
						</div>
					<? endforeach; ?>

					<? $Notifications->markAsRead(); ?>
				<? else: ?>
					<p class="f4 tc pa4 i">
						You have no notifications.
					</p>
				<? endif; ?>
			</div>
		</div>

		<div class="col-xs-4 pb flex flex-column justify-start oy-auto">
			@if (Auth()->User()->isAdmin())
				<div class="ma mv4 bg-white br3 py4 px4">
					<h1 class="ma0 pa0 f2 mb3">
						<i class="fa fa-bullhorn"></i>
						Broadcast New Message
					</h1>

					<notification-broadcast-form
						:initial-message='<?= json_encode(old('message', null)); ?>'
					></notification-broadcast-form>
				</div>
			@endif
		</div>
	</div>

@stop