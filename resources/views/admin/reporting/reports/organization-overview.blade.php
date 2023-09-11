@extends('layouts.report')

@section('report-title')
Organization Overview
@stop

@section('content')
	<div class="flex justify-between mb3">
		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 mt0 pa0">Overview</h3>
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
					<th scope="row" class="tr">Period Products Distributed</th>
					<td><?= number_format($stats->period_products, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Orders Processed</th>
					<td><?= number_format($stats->orders, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Partner Agencies</th>
					<td><?= number_format($stats->agencies, 0); ?></td>
				</tr>
			</table>
		</div>

		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 mt0 pa0">Family Overview</h3>
			<table class="table table-bordered table-striped">
				<tr>
					<th scope="row" class="w-50 tr">Children Served</th>
					<td><?= number_format($stats->child, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="w-50 tr">Menstruators Served</th>
					<td><?= number_format($stats->menstruators, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Families Served</th>
					<td><?= number_format($stats->families, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Military Children Served</th>
					<td><?= number_format($stats->military_children, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="w-25 tr">Military Families Served</th>
					<td><?= number_format($stats->military_families, 0); ?></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="flex justify-between">
		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 pa0">Diapers Distributed</h3>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th class="w-25 tr">Size</th>
						<th>Distributed</th>
					</tr>
				</thead>

				<? foreach ($diaper_sizes as $sz_key => $sz_label): ?>
					<tr>
						<th scope="row" class="tr"><?= $sz_label; ?></th>
						<td><?= number_format($stats->{"{$sz_key}_diapers"}, 0); ?></td>
					</tr>
				<? endforeach; ?>
			</table>
		</div>

		<div class="w-50 fs-no fg-no pr">
			<h3 class="f3 pa0">Pull-ups Distributed</h3>
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
							<th>Distributed</th>
						</tr>
					</thead>

					<? foreach ($pullup_sizes as $sz_key => $sz_label): ?>
						<tr>
							<th scope="row" class="tr"><?= $sz_label; ?></th>
							<td><?= number_format($stats->{"{$sz_key}_pullups_{$gender}"}, 0); ?></td>
						</tr>
					<? endforeach; ?>
				</table>
			<? endforeach; ?>
		</div>
	</div>

	<div class="w-50 fs-no fg-no pr">
		<h3 class="f3 mt0 pa0">Period Products</h3>
		<table class="table table-bordered table-striped">
			<tr>
				<th scope="row" class="w-50 tr">Regular Pads</th>
				<td><?= number_format($stats->regular_Pads, 0); ?></td>
			</tr>
			<tr>
				<th scope="row" class="tr">Overnight Pads</th>
				<td><?= number_format($stats->overnight_pads, 0); ?></td>
			</tr>
			<tr>
				<th scope="row" class="tr">Tampons</th>
				<td><?= number_format($stats->tampons, 0); ?></td>
			</tr>
			<tr>
				<th scope="row" class="tr">Teen Regular Pads</th>
				<td><?= number_format($stats->teen_regular_pads, 0); ?></td>
			</tr>
			<tr>
				<th scope="row" class="tr">Teen Overnight Pads</th>
				<td><?= number_format($stats->teen_overnight_pads, 0); ?></td>
			</tr>
			<tr>
				<th scope="row" class="tr">Post-Partum Pads</th>
				<td><?= number_format($stats->post_partum_pads, 0); ?></td>
			</tr>
			<tr>
				<th scope="row" class="tr">Perineal Cold Packs</th>
				<td><?= number_format($stats->perineal_cold_packs, 0); ?></td>
			</tr>
		</table>
	</div>
@stop