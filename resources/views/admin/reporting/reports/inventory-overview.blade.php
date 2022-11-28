@extends('layouts.report')

@section('report-title')
Inventory Overview
@stop

@section('content')
	<div class="flex justify-between">
		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 tc ma0 pa0"><?= carbon($stats->start->report_date)->format('M jS, Y'); ?></h3>

			<h4 class="f4 mt0">Diaper Inventory</h4>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="w-25 tr">Size</th>
						<th>Qty On-hand</th>
					</tr>
				</thead>

				<? foreach ($diaper_sizes as $sz_key => $sz_label): ?>
					<tr>
						<th scope="row" class="tr"><?= $sz_label; ?></th>
						<td><?= number_format($stats->start->{"{$sz_key}_diapers"}, 0); ?></td>
					</tr>
				<? endforeach; ?>
			</table>

			<h3 class="f4 mt0">Pull-up Inventory</h3>
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
							<th>Qty On-hand</th>
						</tr>
					</thead>

					<? foreach ($pullup_sizes as $sz_key => $sz_label): ?>
						<tr>
							<th scope="row" class="tr"><?= $sz_label; ?></th>
							<td><?= number_format($stats->start->{"{$sz_key}_pullups_{$gender}"}, 0); ?></td>
						</tr>
					<? endforeach; ?>
				</table>
			<? endforeach; ?>
		</div>

		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 ma0 tc pa0"><?= carbon($stats->end->report_date)->format('M jS, Y'); ?></h3>

			<h4 class="f4 mt0">Diapers</h4>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="w-25 tr">Size</th>
						<th>Qty On-hand</th>
					</tr>
				</thead>

				<? foreach ($diaper_sizes as $sz_key => $sz_label): ?>
					<tr>
						<th scope="row" class="tr"><?= $sz_label; ?></th>
						<td><?= number_format($stats->end->{"{$sz_key}_diapers"}, 0); ?></td>
					</tr>
				<? endforeach; ?>
			</table>

			<h3 class="f4 mt0">Pull-up Details</h3>
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
							<th>Qty On-hand</th>
						</tr>
					</thead>

					<? foreach ($pullup_sizes as $sz_key => $sz_label): ?>
						<tr>
							<th scope="row" class="tr"><?= $sz_label; ?></th>
							<td><?= number_format($stats->end->{"{$sz_key}_pullups_{$gender}"}, 0); ?></td>
						</tr>
					<? endforeach; ?>
				</table>
			<? endforeach; ?>
		</div>
	</div>
@stop