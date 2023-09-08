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
				<th>
					Diaper Drive Details
				</th>
				<th  class="tc">Diapers</th>
				<th  class="tc">Pull-ups</th>
				<th class="tc">Total</th>
			</tr>
		</thead>

		<? foreach ($stats as $drive):?>
			<tr>
				<th scope="row">
					<p><?= carbon($drive->adjustment_datetime)->format('M j, Y'); ?></p>
					<p class="wtl"><?= nl2br(e($drive->adjustment_note)); ?></p>
				</th>
				<td ><?= number_format($drive->total_diapers, 0); ?></td>
						<td >
							<?= number_format($drive->total_pullups, 0); ?>
						</td>
					<td ><?= number_format($drive->total_donated, 0); ?></td>
			</tr>
		<? endforeach; ?>
	</table>
@stop