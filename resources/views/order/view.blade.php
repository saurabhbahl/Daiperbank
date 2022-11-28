<? use App\ProductCategory; ?>

@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">
			<a href="<?= route('order.index'); ?>">Orders</a>
		</p>
		<p class="separator">/</p>
		<p class="crumb">Order Details</p>
	</div>

	<agent-order-view
		class="db flex-auto flex justify-start content-stretch o-hidden"
		:initial-order='<?= e($Order->toJson()); ?>'
		:product-categories='<?= $Categories->toJson(); ?>'
	></agent-order-view>

@stop