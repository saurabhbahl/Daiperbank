<? use App\ProductCategory; ?>

@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">
			<? if ($isAdmin): ?>
				<a href="<?= route('admin.order.index'); ?>">Orders</a>
			<? else: ?>
				<a href="<?= route('order.index'); ?>">Orders</a>
			<? endif; ?>
		</p>
		<p class="separator">/</p>
		<p class="crumb">Order Details</p>
	</div>

	<admin-order-view
		class="db flex-auto flex justify-start content-stretch o-hidden"
		:initial-order='<?= e($Order->toJson()); ?>'
		:initial-order-summary='<?= $Order->ProductSummary->toJson(); ?>'
		:pickup-dates='<?= $PickupDates->toJson(); ?>'
		:product-categories='<?= $Categories->toJson(); ?>'
		:is-admin="<?= json_encode($isAdmin); ?>"
	></admin-order-view>

@stop