@extends('layouts.report')

@section('orientation')
landscape
@stop

@section('report-title')
Diaper Drive Report
@stop

@section('content')

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

		<? foreach ($stats as $drive):?>
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