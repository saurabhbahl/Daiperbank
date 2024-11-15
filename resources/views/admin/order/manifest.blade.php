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

	<table class="w-100">
		<tr>
			<td class="w-50">
				<p class="ma0 pa0 pb b f2">Order Manifest</p>
			</td>
			<td>
				<p class="ma0 pa0 pb tr b f2">
					<span class="wtl">Pickup Date:</span>
					<?= $PickupDate->pickup_date->format('D, M j, Y'); ?>
				</p>
			</td>
		</tr>
	</table>

	<table class="table table-striped table-condensed table-bordered">
		<thead>
			<tr>
				<th width="20%">Order Number</th>
				<th width="20%">Agency</th>
				<th width="10%">Individual</th>
				<th width="10%">Packed On</th>
				<th width="15%">Packed By</th>
				<th width="10%">Pickup At</th>
				<th width="15%">Picked Up By</th>
			</tr>
		</thead>

		<tbody>
			<? foreach ($Orders as $Order): ?>
				<tr>
					<td>#<?= e($Order->full_id); ?></td>
					<td><?= e($Order->Agency->id_prefix); ?>
					<td><?= number_format($Order->ApprovedChild->count(), 0); ?></td>
					<td class="nowrap tc b">&nbsp;&nbsp;&nbsp; / &nbsp;&nbsp;&nbsp; / <span class="wtn">20</span>&nbsp;&nbsp;</td>
					<td></td>
					<td class="nowrap tc b f2 lnh1"> : </td>
					<td></td>
				</tr>
			<? endforeach; ?>
		</tbody>
	</table>
</body>
</html>