@extends('layouts.report')

@section('orientation')
landscape
@stop

@section('report-title')
Diaper Drive Report
@stop

@section('content')
	<h3 class="f3 mt0 pa0">Donations Overview</h3>
	<table class="table table-bordered table-striped">
		<tr>
			<th scope="row" class="w-50 tr">Donations</th>
			<td><?= number_format($stats['Aggregate'][0]->donations, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Total Diapers</th>
			<td><?= number_format($stats['Aggregate'][0]->total_diapers, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Total Pull-ups</th>
			<td><?= number_format($stats['Aggregate'][0]->total_pullups, 0); ?></td>
		</tr>
		<tr>
			<th scope="row" class="tr">Total Period Products</th>
			<td><?= number_format($stats['Aggregate'][0]->total_period_products, 0); ?></td>
		</tr>
	</table>
	<table class="table table-bordered table-striped">
		
		<thead>
			<tr>
				<th rowspan="2">
					Diaper Drive Details
				</th>
				<th colspan="1" class="tc">Diapers</th>
				<th colspan="2"class="tc">Pull-ups</th>
				<th colspan="2"class="tc">Period Products</th>
				<th colspan="1" class="tc">Total</th>
			</tr>
		</thead>

		<? foreach ($stats['Detail'] as $drive):?>
			<tr>
				<th scope="row">
					<p><?= carbon($drive->adjustment_datetime)->format('M j, Y'); ?></p>
					<p class="wtl"><?= nl2br(e($drive->adjustment_note)); ?></p>
				</th>
				<td colspan="1"><?= number_format($drive->total_diapers, 0); ?></td>
				<td colspan="2">
					<?= number_format($drive->total_pullups, 0); ?>
				</td>
				<td colspan="2">
					<?= number_format($drive->total_period, 0); ?>
				</td>
				<td colspan="1"><?= number_format($drive->total_donated, 0); ?></td>
			</tr>
		<? endforeach; ?>
	</table>
@stop