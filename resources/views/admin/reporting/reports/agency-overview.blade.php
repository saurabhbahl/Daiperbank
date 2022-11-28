@extends('layouts.report')

@section('report-title')
Agency Report: <span class="wtl"><?= e($Agency->name); ?></span>
@stop

@section('content')
	<h3 class="f3"><?= e($Agency->name); ?></h3>

	<table class="table table-bordered table-striped">
		<tr>
			<th scope="row" class="w-50 tr">Diapers Distributed</th>
			<td><?= number_format($stats->diapers, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Pull-ups Distributed</th>
			<td><?= number_format($stats->pull_ups, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Children Served</th>
			<td><?= number_format($stats->children, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Families Served</th>
			<td><?= number_format($stats->families, 0); ?></td>
		</tr>
	</table>
@stop