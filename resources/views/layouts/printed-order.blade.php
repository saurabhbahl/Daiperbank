<!DOCTYPE html>
<html class="m0 p0">
<head>
<title></title>
<base href="{{ url('/') }}">
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
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
			<img src="/img/logo.png" style="max-height: 50px;">
		</div>

		<div class="w-75 tr f4">
			<p class="b">Order Receipt</p>
			<p>
				<span class="b">Order ID:</span>
				@yield('order-id')
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

@yield('js')
</body>
</html>
