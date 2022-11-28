<? use App\Order; ?>

@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Order Fulfillment</p>
		<p class="separator">/</p>
		<p class="crumb">Upcoming Pickups</p>
	</div>

	<admin-exported-pickups
		:initial-pickup-dates='<?= e($PickupDates->toJson()); ?>'
	></admin-exported-pickups>

@stop