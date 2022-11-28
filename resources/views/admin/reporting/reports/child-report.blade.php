@extends('layouts.report')

@section('orientation')
landscape
@stop

@section('report-title')
Child Report
@stop

@section('report-range')
<span class="wtl">
	<? if ($end->diffInMonths() < 40): ?>
		<?= $end->diffInMonths() . " months"; ?>
	<? else: ?>
		<?= $end->diffInYears() . " years"; ?>
		<? if ($end->diffInMonths() % 12 != 0): ?>
			, <?= $end->diffInMonths() % 12; ?> months
		<? endif; ?>
	<? endif; ?>

	&mdash;

	<? if ($start->diffInMonths() < 40): ?>
		<?= $start->diffInMonths() . " months"; ?>
	<? else: ?>
		<?= $start->diffInYears() . " years"; ?>
		<? if ($start->diffInMonths() % 12 != 0): ?>
			, <?= $start->diffInMonths() % 12; ?> months
		<? endif; ?>
	<? endif; ?>
</span>
@stop

@section('content')
	<table class="table table-bordered table-striped">
		<thead>
			<tr>
				<th rowspan="2" valign="bottom">Child</th>
				<th rowspan="2" valign="bottom">DOB</th>
				<th rowspan="2" valign="bottom">Age (Mos)</th>
				<th rowspan="2" valign="bottom">Agency</th>
				<th colspan="3" class="tc" style="border-left-width: 3px;">Lifetime</th>
				<th colspan="3" class="tc" style="border-left-width: 3px;">After 36 Months Old</th>
			</tr>
			<tr>
				<th style="border-left-width: 3px;">Orders</th>
				<th>Diapers</th>
				<th>Pull-ups</th>
				<th style="border-left-width: 3px;">Orders</th>
				<th>Diapers</th>
				<th>Pull-ups</th>
			</tr>
		</thead>

		<? foreach ($stats as $Child): ?>
			<tr>
				<th scope="row">
					<?= e($Child->child_identifier); ?>
				</th>
				<td><?= carbon($Child->dob)->format('Y-m-d'); ?></td>
				<td><?= carbon($Child->dob)->diffInMonths(); ?></td>
				<td><?= e($Child->agency_name); ?></td>
				<td style="border-left-width: 3px;"><?= number_format($Child->lifetime_orders, 0); ?></td>
				<td><?= number_format($Child->lifetime_diapers, 0); ?></td>
				<td><?= number_format($Child->lifetime_pullups, 0); ?></td>
				<td style="border-left-width: 3px;"><?= number_format($Child->{'36mo_orders'}, 0); ?></td>
				<td><?= number_format($Child->{'36mo_diapers'}, 0); ?></td>
				<td><?= number_format($Child->{'36mo_pullups'}, 0); ?></td>
			</tr>
		<? endforeach; ?>
	</table>
@stop