@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">
			<? if (Auth()->User()->isAdmin()): ?>
				<a href="<?= route('admin.order.index'); ?>">Orders</a>
			<? else: ?>
				<a href="<?= route('order.index'); ?>">Orders</a>
			<? endif; ?>
		</p>
		<p class="separator">/</p>
		<p class="crumb">New Order</p>
	</div>

	<order-creator
		class="db flex-auto flex justify-start content-stretch o-hidden"
		:all-children='<?= e($Children->toJson()); ?>'
		:available-pickup-dates='<?= e($PickupDates->toJson()); ?>'
		:available-product-categories='<?= e($ProductCategories->toJson()); ?>'
		:initial-order="<?= e($Order->toJson()); ?>"
	></order-creator>
@stop