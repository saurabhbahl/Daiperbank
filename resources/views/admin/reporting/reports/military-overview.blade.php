@extends('layouts.report')

@section('report-title')
Military Overview
@stop

@section('content')
	<table class="table table-bordered table-striped">
		<tr>
			<th scope="row" class="w-25 tr">Diapers Distributed</th>
			<td><?= number_format($stats->military_diapers, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Pull-ups Distributed</th>
			<td><?= number_format($stats->military_pull_ups, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Period Product Distributed</th>
			<td><?= number_format($stats->military_period_products, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Children Served</th>
			<td><?= number_format($stats->military_children, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Menstruators Served</th>
			<td><?= number_format($stats->military_mens, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Families Served</th>
			<td><?= number_format($stats->military_families, 0); ?></td>
		</tr>
	</table>
@stop