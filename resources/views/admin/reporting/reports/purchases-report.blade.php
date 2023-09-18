@extends('layouts.report')

@section('report-title')
Donation Report
@stop

@section('content')
<h3 class="f3 mt0 pa0">Purchase Overview</h3>
	<table class="table table-bordered table-striped">
		<tr>
			<th scope="row" class="w-50 tr">Purchase</th>
			<td><?= number_format($stats['Aggregate'][0]->purchase, 0); ?></td>
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
			<th scope="row" class="tr">Total Period Product</th>
			<td><?= number_format($stats['Aggregate'][0]->total_periods, 0); ?></td>
		</tr>
	</table>

	<div class="flex justify-between">
		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 pa0">Diaper Purchase</h3>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="w-25 tr">Size</th>
						<th>Qty Purchased</th>
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
			<h3 class="f3 pa0">Pull-up Purchase</h3>
			<? foreach($genders as $gender): ?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th colspan="2">
								<i class="fa fa-<?= $gender == 'boy'? 'male' : 'female'; ?>"></i>
								<?= ucwords($gender); ?> Pull-ups
							</th>
						</tr>

						<tr>
							<th class="w-25 tr">Size</th>
							<th>Qty Purchased</th>
						</tr>
					</thead>

					<? foreach ($pullup_sizes as $sz_key => $sz_label): ?>
						<tr>
							<th scope="row" class="tr"><?= $sz_label; ?></th>
							<td><?= number_format($stats['Aggregate'][0]->{"{$sz_key}_pullups_{$gender}"}, 0); ?></td>
						</tr>
					<? endforeach; ?>
				</table>
			<? endforeach; ?>
		</div>
	</div>
	
	<div class="flex justify-between">
		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 pa0">Period Products Donations</h3>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="w-25 tr">Size</th>
						<th>Qty Donated</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th scope="row" class="tr">Regular Pads</th>
						<td><?= number_format($stats['Aggregate'][0]->regular_Pads, 0); ?></td>
					</tr>
					<tr>
						<th scope="row" class="tr">Overnight Pads</th>
						<td><?= number_format($stats['Aggregate'][0]->overnight_pads, 0); ?></td>
					</tr>
					<tr>
						<th scope="row" class="tr">Tampons</th>
						<td><?= number_format($stats['Aggregate'][0]->tampons, 0); ?></td>
					</tr>
					<tr>
						<th scope="row" class="tr">Teen Regular Pads</th>
						<td><?= number_format($stats['Aggregate'][0]->teen_regular_pads, 0); ?></td>
					</tr>
					<tr>
						<th scope="row" class="tr">Teen Overnight Pads</th>
						<td><?= number_format($stats['Aggregate'][0]->teen_overnight_pads, 0); ?></td>
					</tr>
					<tr>
						<th scope="row" class="tr">Post Partum Pads</th>
						<td><?= number_format($stats['Aggregate'][0]->post_partum_pads, 0); ?></td>
					</tr>
					<tr>
						<th scope="row" class="tr">Perineal Cold Packs</th>
						<td><?= number_format($stats['Aggregate'][0]->perineal_cold_packs, 0); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<h3 class="f3 pa0">Purchase Details</h3>
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th>
					Details
				</th>
				<th>Diapers Purchase</th>
				<th>Pull-ups Purchase</th>
				<th>Period Product Purchase</th>
				<th>Total Purchase</th>
			</tr>
		</thead>

		<? foreach ($stats['Detail'] as $detail): ?>
			<tr>
				<th scope="row">
					<p><?= carbon($detail->adjustment_datetime)->format('M j, Y'); ?></p>
					<p class="wtl"><?= nl2br(e($detail->adjustment_note)); ?></p>
				</th>
				<td><?= number_format($detail->total_diapers, 0); ?></td>
				<td><?= number_format($detail->total_pullups, 0); ?></td>
				<td><?= number_format($detail->total_periods, 0); ?></td>
				<td><?= number_format($detail->total_donated, 0); ?></td>
			</tr>
		<? endforeach; ?>
	</table>
@stop