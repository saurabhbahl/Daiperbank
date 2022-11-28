@extends('layouts.report')

@section('report-title')
Location Report: <span class="wtl"><?= $stats->zip; ?></span>
<br>
<span class="wtl"><?= "{$stats->county} County, {$stats->city} {$stats->state_abbr}"; ?></span>
@stop

@section('content')
	<div class="flex justify-around">
		<div class="w-50 fg-no fs-no pr">
			<h3 class="f3">Diaper Stats</h3>

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
		</div>

		<div class="w-50 fg-no fs-no pl">
			<h3 class="f3">Military Service</h3>

			<table class="table table-bordered table-striped">
				<tr>
					<th scope="row" class="w-60 tr">Military Children</th>
					<td><?= number_format($stats->military_children, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Military Families</th>
					<td><?= number_format($stats->military_families, 0); ?></td>
				</tr>
			</table>

			<h3 class="f3">Children Served by Race</h3>
			<table class="table table-bordered table-striped">
				<tr>
					<th scope="row" class="w-60 tr">American Indian or Alaska Native</th>
					<td><?= number_format( $stats->{'American Indian or Alaska Native'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Asian</th>
					<td><?= number_format( $stats->{'Asian'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Black or African American</th>
					<td><?= number_format( $stats->{'Black or African American'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Native Hawaiin or Pacific Islander</th>
					<td><?= number_format( $stats->{'Native Hawaiin or Pacific Islander'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">White</th>
					<td><?= number_format( $stats->{'White'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Two or More Races</th>
					<td><?= number_format( $stats->{'Two or More Races'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Other</th>
					<td><?= number_format( $stats->{'Other'} ?? 0, 0); ?></td>
				</tr>
			</table>

			<h3 class="f3">
				Children Served by Ethnicity
			</h3>
			<table class="table table-bordered table-striped">
				<tr>
					<th scope="row" class="w-60 tr">Hispanic or Latino</th>
					<td><?= number_format( $stats->{'Hispanic or Latino'} ?? 0, 0); ?></td>
				</tr>
				<tr>
					<th scope="row" class="tr">Non-Hispanic or Latino</th>
					<td><?= number_format( $stats->{'Non-Hispanic or Latino'} ?? 0, 0); ?></td>
				</tr>
			</table>
		</div>
	</div>
@stop