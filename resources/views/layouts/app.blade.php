<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<? if (config('app.debug')): ?>
		<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>
		<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>
	<? endif; ?>

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'Laravel') }}</title>

	<!-- Styles -->
	<link href="{{ mix('css/app.css') }}?<?= time(); ?>" rel="stylesheet">

	<script type="text/javascript">
	window.HSDB = { lowWater: <?= json_encode(config('hsdb.low_water')); ?> };
	</script>

	@yield('head')
</head>
<body>
	<div id="app">
		<hs--modal></hs--modal>

		<? if (config('app.env') !== 'production'): ?>
			<div class="tc bg-washed-red red pa4">
				<p class="f3">
					This is a <strong>STAGING</strong> environment.
				</p>
				<p class="f4">
					Changes & Data here is completely separate from the LIVE application and should be considered ephemeral and temporary.
				</p>
			</div>
		<? endif; ?>

		@include('flash::message')

		<nav class="fs-no flex justify-between items-stretch">
			<div class="pa4 fg-no">
				<a href="<?= route('home'); ?>">
					<img src="/img/logo.png" style="max-height: 50px;">
				</a>
			</div>

			<div class="flex-auto flex justify-between">
				@if (Auth::guest())
					<div class="nav-item">
						<a href="{{ route('login') }}">
							<i class="fa fa-sign-in"></i>
							Login
						</a>
					</div>
				@else
					<div class="nav-item">
						<a href="<?= route('home'); ?>">
							<i class="fa fa-home"></i>
							Home
						</a>
					</div>

					@if (Auth()->User()->isAdmin())
						@include('partials.nav.admin')
					@else
						@include('partials.nav.agency')
					@endif

					<div class="nav-item dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
							{{ Auth::user()->name }} <span class="dropdown-handle"><i class="fa fa-caret-down"></i></span>
						</a>

						<ul class="dropdown-menu nav-links" role="menu">
							@if (!Auth()->User()->isAdmin())
                            <li>
                                <a href="{{route('agency.profile.index')}}">
									<i class="fa fa-user"></i>
									Profile
								</a>
                            </li>
							@endif
							<li>
								<a href="#"	id="logout-button">
									<i class="fa fa-sign-out"></i>
									Logout
								</a>

								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
								</form>
							</li>
						</ul>
					</div>

					<div class="nav-item dropdown">
						<notification-nav-item
							:unread-count="<?= !empty($Notifications)? $Notifications->unread() : 0; ?>"
							:newest-timestamp="<?= json_encode((!empty($Notifications) && $NewestUnread = $Notifications->firstUnread())? $NewestUnread->created_at->format('U') : null); ?>"
							:oldest-timestamp="<?= json_encode((!empty($Notifications) && $OldestUnread = $Notifications->lastUnread())? $OldestUnread->created_at->format('U') : null); ?>">
						></notification-nav-item>

						<ul class="dropdown-menu notifications" role="menu" style="width: 350px; left: auto; right: 0;">
							<? if (!empty($Notifications) && $Notifications->count()): ?>
								<? foreach ($Notifications as $Notification): ?>
									<li class="pa4 bb b--black-20">
										<p class="muted f4 tr">
											<?= e($Notification->humanReadablePostedTime()); ?>
										</p>

										<?= $Notification->render(); ?>
									</li>
								<? endforeach; ?>

								<? if ($Notifications->count() && $Notifications->hasMore): ?>
									<li class="pa4 bb b--black-20">
										<a href="<?= route('notifications.index'); ?>">Read all...</a>
									</li>
								<? endif; ?>
							<? else: ?>
								<li class="pa4">
									<p class="muted i">You have no notifications.</p>
								</li>
							<? endif; ?>
						</ul>
					</div>
				@endif
			</div>
		</nav>

		@yield('content')
	</div>

	<!-- Scripts -->
	<script src="{{ mix('js/app.js') }}?<?= time(); ?>"></script>
	@yield('js')

	<script>
	(function($) {
		$(function() {
			$('div.alert').not('.alert-important').delay(7000).fadeOut(350);

			$('#logout-button').on('click', function(evt) {
				event.preventDefault();
				$('#logout-form').submit();
				return false
			});
		});
	})(jQuery);
	</script>
</body>
</html>
