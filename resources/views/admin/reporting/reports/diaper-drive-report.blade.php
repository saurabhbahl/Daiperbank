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

	<div class="flex justify-between">
		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 pa0">Diaper Details</h3>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="w-25 tr">Size</th>
						<th>Qty Donated</th>
					</tr>
				</thead>

				<? foreach ($diaper_sizes as $sz_key => $sz_label): ?>
					<tr>
						<th scope="row" class="tr"><?= $sz_label; ?></th>
						<td><?= number_format($stats['Aggregate'][0]->{"{$sz_key}_diapers"}, 0); ?></td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>

		<div class="w-50 fs-no fg-no pl">
			<h3 class="f3 pa0">Pull-up Details</h3>
			<? //foreach($genders as $gender): ?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th colspan="2">
								Pull-ups
							</th>
						</tr>

						<tr>
							<th class="w-25 tr">Size</th>
							<th>Qty Donated</th>
						</tr>
					</thead>

					<? foreach ($pullup_sizes as $sz_key => $sz_label): ?>
						<tr>
							<th scope="row" class="tr"><?= $sz_label; ?></th>
							<td><?php
								$pullups_boy = isset($stats['Aggregate'][0]->{"{$sz_key}_pullups_boy"}) ? $stats['Aggregate'][0]->{"{$sz_key}_pullups_boy"} : 0;
								$pullups_girl = isset($stats['Aggregate'][0]->{"{$sz_key}_pullups_girl"}) ? $stats['Aggregate'][0]->{"{$sz_key}_pullups_girl"} : 0;
								echo $pullups_boy+$pullups_girl;
								?>
							</td>
						</tr>
					<? endforeach; ?>
				</table>
			<? //endforeach; ?>
		</div>
	</div>

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