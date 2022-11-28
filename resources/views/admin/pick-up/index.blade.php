@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">Pick-up Schedule</p>

		<? /*
		// the calendar view is pretty sufficient for now,
		// I don't think we need the list view as well.
		<div class="pull-right">
			<pickup-date-view-selector defaultView='calendar' />
		</div>
		 */ ?>
	</div>

	<div class="flex-auto flex o-hidden pxr">
		<pickup-date-list
			:initial-pickup-dates='<?= e(json_encode($PickupDates, JSON_FORCE_OBJECT)); ?>'
			initial-month='<?= e($list_month); ?>'
		></pickup-date-list>
	</div>
@stop