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
				<th colspan="10" class="tc">Diapers</th>
				<th colspan="3" class="tc" style="border-left-width: 3px;"><i class="fa fa-male"></i> Boy Pull-ups</th>
				<th colspan="3" class="tc" style="border-left-width: 3px;"><i class="fa fa-female"></i> Girl Pull-ups</th>
			</tr>
			<tr>
				<? foreach ($diaper_sizes as $sz): ?>
					<th style="width: 5%;"><?= $sz; ?></th>
				<? endforeach; ?>

				<? $thick_border = true; ?>
				<? foreach ($genders as $g): ?>
					<? foreach ($pullup_sizes as $sz): ?>
						<th
							<? if ($thick_border): $thick_border = false; ?>
								style="border-left-width: 3px; width: 5%;"
							<? else: ?>
								style="width: 5%;"
							<? endif; ?>
						><?= $sz; ?></th>
					<? endforeach; ?>
					<? $thick_border = true; ?>
				<? endforeach; ?>
			</tr>
		</thead>

		<? foreach ($stats as $drive): ?>
			<tr>
				<th scope="row">
					<p><?= carbon($drive->adjustment_datetime)->format('M j, Y'); ?></p>
					<p class="wtl"><?= nl2br(e($drive->adjustment_note)); ?></p>
				</th>
				<? foreach ($diaper_sizes as $sz_key => $sz_label): ?>
					<td><?= number_format($drive->{"{$sz_key}_diapers"}, 0); ?></td>
				<? endforeach; ?>
				<? $thick_border = true; ?>
				<? foreach ($genders as $gender): ?>
					<? foreach ($pullup_sizes as $sz_key => $sz_label): ?>
						<td
							<? if ($thick_border): $thick_border = false; ?>
								style="border-left-width: 3px;"
							<? endif; ?>>
							<?= number_format($drive->{"{$sz_key}_pullups_{$gender}"}, 0); ?>
							
						</td>
					<? endforeach; ?>
					<? $thick_border = true; ?>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</table>
@stop