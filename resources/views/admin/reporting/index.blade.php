@extends('layouts.app')

@section('head')

<link rel="stylesheet" type="text/css" href="/bower/jquery-ui/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" type="text/css" href="/bower/jquery-ui/themes/smoothness/jquery.ui.theme.css" />
<link rel="stylesheet" type="text/css" href="/bower/jquery-ui-daterangepicker/jquery.comiseo.daterangepicker.css" />
@stop

@section('js')
<script src="/bower/jquery-ui/jquery-migrate-3.0.0.js"></script>
<script src="/bower/jquery-ui/ui/minified/jquery-ui.min.js"></script>
<script src="/bower/jquery-ui-daterangepicker/jquery.comiseo.daterangepicker.js"></script>
<script type="text/javascript">
$(function() {
	window.app.$refs['admin-reports'].selectReport('org');
});
</script>
@stop

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">Reporting</p>
	</div>

	<admin-reports ref="admin-reports"></admin-reports>
	<? /*
	<div class="flex-auto bg-white">
		<div class="col-s-4 col-xs-6 pt3">
			<?= Form::open(['method' => 'post']); ?>
				<div class="field mb">
					<label for="report">Choose a report to export:</label>
					<select name="report" class="form-control">
						<option>Select one</option>
						<? foreach ($reports as $report_name => $report_label): ?>
							<option value="<?= e($report_name); ?>"><?= e($report_label); ?></option>
						<? endforeach; ?>
					</select>
				</div>

				<div class="flex justify-between">
					<div class="field mb flex-auto">
						<label for="start-date" class="db">Report start date:</label>
						<input type="date" class="form-control" name="start-date" id="start-date" value="<?= old('start-date', carbon('-1 month')->format('Y-m-01')); ?>">
					</div>

					<div class="field mb flex-auto ml3">
						<label for="end-date" class="db">Report end date:</label>
						<input type="date" class="form-control" name="end-date" id="end-date" value="<?= old('end-date', carbon()->format('Y-m-d')); ?>">
					</div>
				</div>

				<div class="field mb">
					<button stype="submit" class="btn btn-primary btn-block">Download Report</button>
				</div>
			<?= Form::close(); ?>

			<div class="field mb">
				<p class="small muted">
					<strong>*Note*</strong>
					The child report start/end dates only affect the orders that are reported for each child. All non-archived children
					are always reported, and child age is always based on the report date.
				</p>
			</div>
		</div>
		*/ ?>
<? /*

		<div>
			<h2>Organization Overview</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="org-report">

				<label for="start-date">Start date</label>

				<label for="end-date">End date</label>
				<input type="date" name="end-date" id="end-date" value="<?= old('end-date', null); ?>">

				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>

		<div>
			<h2>Agency Overview</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="agency-report">

				<label for="start-date">Start date</label>
				<input type="date" name="start-date" id="start-date" value="<?= old('start-date', null); ?>">

				<label for="end-date">End date</label>
				<input type="date" name="end-date" id="end-date" value="<?= old('end-date', null); ?>">

				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>

		<div>
			<h2>Location Overview</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="location-report">

				<label for="start-date">Start date</label>
				<input type="date" name="start-date" id="start-date" value="<?= old('start-date', null); ?>">

				<label for="end-date">End date</label>
				<input type="date" name="end-date" id="end-date" value="<?= old('end-date', null); ?>">

				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>

		<div>
			<h2>Child Report</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="child-report">

				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>

		<div>
			<h2>Donation Report</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="donation-report">

				<label for="start-date">Start date</label>
				<input type="date" name="start-date" id="start-date" value="<?= old('start-date', null); ?>">

				<label for="end-date">End date</label>
				<input type="date" name="end-date" id="end-date" value="<?= old('end-date', null); ?>">
				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>

		<div>
			<h2>Diaper Drive Report</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="diaper-drive-report">

				<label for="start-date">Start date</label>
				<input type="date" name="start-date" id="start-date" value="<?= old('start-date', null); ?>">

				<label for="end-date">End date</label>
				<input type="date" name="end-date" id="end-date" value="<?= old('end-date', null); ?>">

				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>

		<div>
			<h2>Inventory Report</h2>
			<?= Form::open(['method' => 'post']); ?>
				<input type="hidden" name="report" value="inventory-report">

				<label for="start-date">Start date</label>
				<input type="date" name="start-date" id="start-date" value="<?= old('start-date', null); ?>">

				<label for="end-date">End date</label>
				<input type="date" name="end-date" id="end-date" value="<?= old('end-date', null); ?>">

				<button type="submit" class="btn btn-primary">Download Report</button>
			<?= Form::close(); ?>
		</div>
	</div>
		*/ ?>
@stop