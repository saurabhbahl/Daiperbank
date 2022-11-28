@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb crumb--main">Inventory</p>
		<p class="separator">/</p>
		<p class="crumb">
			Create Adjustment
		</p>

		<br>

		<a href="<?=route('admin.inventory.index');?>">Back</a>
	</div>

	<div class="flex-auto bg-white">
		<?= Form::open(['method' => 'post']); ?>
			<inventory-adjustment-form
				class="pa4"
				:type-map='<?= json_encode($typeMap); ?>'
				:initial-errors="<?= e( $errors->toJson()); ?>"
				:initial-values="<?= e(json_encode(old(null))); ?>"
				:product-categories='<?= $ProductCategories->toJson(); ?>'
			></inventory-adjustment-form>
		<?= Form::close(); ?>
	</div>

@stop
