<!DOCTYPE html>
<html class="m0 p0">
<head>
<title></title>
<!-- <base href="{{ url('/') }}"> -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<!-- <link href="http://localhost/hs/public/css/app.css" rel="stylesheet"> -->
<style type="text/css">
body, html { background: #FFF; }
@page {
	size: @yield('orientation', 'portrait');

	@top {
		content: element(header);
	}
	
	@bottom-right {
		content: counter(page) " of " counter(pages);
	}
}

header {
	position: running(header);
}

thead { display: table-header-group }
tfoot { display: table-row-group }
tr { page-break-inside: avoid }
</style>
</head>
<body class="m0 p0">

	<header class="flex justify-between mb">
		<div class="w-25">
			<img src="<? echo public_path('img/logo.png'); ?>" style="max-height: 50px;">
		</div>

		<div class="w-75 tr f4">
			<p class="b">@yield('report-title')</p>
			<p>
				<span class="b">Report Range:</span>
				@section('report-range')
					<? if ($start && $end): ?>
						<span class="wtl"><?= $start->format('M jS, Y'); ?> <span class="i">through</span> <?= $end->format('M jS, Y'); ?></span>
					<? endif; ?>
				@stop
				@yield('report-range')
			</p>
			<p class="f5 i wtl">
				Generated on
				@section('generated-at')
					<?= carbon()->format('M jS, Y @ h:i a'); ?>
				@stop
				@yield('generated-at')
			</p>
		</div>
	</header>

	<div class=""><!-- the report data -->
		@yield('content')
	</div>

</body>
</html>
