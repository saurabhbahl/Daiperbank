<!DOCTYPE html>
<html>
<head>
<title></title>
<link href="{{ asset('css/app.css') }}" rel="stylesheet">
<style type="text/css">
@page {
	size: landscape;

	@bottom-right {
		content: counter(page) " of " counter(pages);
	}
}
thead { display: table-header-group }
tfoot { display: table-row-group }
tr { page-break-inside: avoid }
</style>
</head>
<body class="pa">


</body>
</html>