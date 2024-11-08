@extends('layouts.app')

@section('content')
	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto oy-auto pa3">
			Admin Home
		</div>

		<div class="col-xs-4 pb oy-auto">
			<div class="ma mv4 bg-white br3 py4 px4">

				<p class="f2 b bb bw2 mb3">
					Notifications
				</p>

				<? if (empty($Notifications) || 0 == $Notifications->count()): ?>
					<p class="tc i wtl pa4">
						No notifications
					</p>
				<? else: ?>
					<? foreach ($Notifications as $Notification): ?>
						<div class="bb b--black-20 pv3 mb3">
							<p class="f4 i tr muted">
								<span class="">Posted on</span>
								<span><?= e($Notification->humanReadablePostedTime()); ?></span>
							</p>
							<?= $Notification->render(); ?>
						</div>
					<? endforeach; ?>
				<? endif; ?>

			</div>
		</div>
	</div>

@stop