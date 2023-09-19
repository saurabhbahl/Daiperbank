@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb crumb--main">Inventory</p>
		<p class="separator">/</p>
		<p class="crumb">
			Update Adjustment
		</p>

		<br>

		<a href="<?=route('admin.inventory.index');?>">Back</a>
	</div>

	<div class="flex-auto bg-white">
		<?= Form::open(['method' => 'post']); ?>
			<inventory-adjustment-update-form
				class="pa4"
				:type-map='<?= json_encode($typeMap); ?>'
				:initial-errors="<?= e( $errors->toJson()); ?>"
				:initial-values="<?= e(json_encode(old(null))); ?>"
				:initial-data="<?= e($Data->toJson()); ?>"
				:product-categories='<?= $ProductCategories->toJson(); ?>'
			></inventory-adjustment-update-form>
		<?= Form::close(); ?>
	</div>

@stop
