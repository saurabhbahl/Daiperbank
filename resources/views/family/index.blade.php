@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">
			<a href="<?=route('home');?>">Children Management</a>
		</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden pxr">
		<div class="col-xs-7 pa0 bg-white br b--black-20 flex-auto o-hidden flex flex-column justify-stretch">
			<child-list
				ref="child-list"
				class="child-list fg fs oy-auto"
				:initial-children="{{ json_encode($Children->items()) }}">

				<template slot="item" slot-scope="Child">
					<a class="view-child-detail db flex bb b--black-20 pa3 a-plain a:hov-nou hov:bg-washed-blue clickable"
						href="#"
						:data-id="Child.id"
						:key="Child.id">

						<p class="w-25">
							<i class="fa" :class="{ 'fa-female': Child.gender === 'f', 'fa-male': Child.gender === 'm'}"></i>
							<span class="b">@{{ Child.name }}</span>
							<br>
							<span v-show="Child.uniq_id" class="f4 i muted">#@{{ Child.uniq_id }}</span>
						</p>

						<p class="w-25">
							<span class="b">DOB:</span>
							@{{ Child.dob | formatDate("MM/DD/YYYY") }}
							<br>
							<!--<span class="b">Age:</span>
							@{{ Child.age_str }}-->
						</p>

						<p class="w-25">
							<span class="b">Zip Code:</span>
							@{{ Child.zip }}
						</p>

						<p class="flex-auto self-center tr">
							<i class="fa fa-chevron-right"></i>
						</p>
					</a>
				</template>
			</child-list>

			<? if ($Children->lastPage() > 1): ?>
				<div class="fg-no fs-no pv3 flex justify-center bg-washed-blue bt b--black-20">
					<a href="<?= $Children->previousPageUrl() ?? "#"; ?>"
						class="btn btn-default mr <?= ! $Children->previousPageUrl()? 'mod-disabled' : ''; ?>">
						<i class="fa fa-chevron-left"></i>
						Previous Page
					</a>

					<? $start_page = max($Children->currentPage() - 2, 1); ?>
					<? $end_page = min($start_page + 4, $Children->lastPage()); ?>
					<? foreach (range($start_page, $end_page) as $pg): ?>
						<a href="<?= $Children->url( $pg ); ?>"
							class="btn btn-default mr <?= $pg == $Children->currentPage()? 'b' : ''; ?>">
							<?= $pg; ?>
						</a>
					<? endforeach; ?>

					<a href="<?= $Children->nextPageUrl() ?? "#"; ?>"
						class="btn btn-default <?= ! $Children->nextPageUrl()? 'mod-disabled' : ''; ?>">
						Next Page
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			<? endif; ?>
		</div>

		<div class="col-xs-5 ">

			<div class="fs-no fg-no pv4 bb b--black-20">
				<a href="#" class="add-child-btn btn btn-block btn-default">Add Child</a>
			</div>

			<div class="mv4 bg-white br3 py4 px4">
				<form method="get">
					<div class="mb">
						<p class="b">Search</p>
						<input name="filter[search]"
							type="search"
							class="form-control"
							placeholder="Search for children..."
							value="<?= e(request()->input('filter.search')); ?>"
						/>
						<p class="tr"><small><a href="#">Search tips</a></small></p>
					</div>

					<div class="mb">
						<p class="b">Parent/Guardian</p>
						<guardian-select
							ref="guardian-select"
							:guardians='<?= e($Guardians->toJson()); ?>'
							:default='<?= json_encode(array_map('intval', explode(',', request()->input('filter.guardian', '')))); ?>'
							@keyup.enter.stop.prevent="false"
						></guardian-select>
						<input type="hidden"
							id="guardian_filter"
							name="filter[guardian]"
							value="<?= request()->input('filter.guardian', ''); ?>">
					</div>


					<div class="pt">
						<input type="hidden" name="page" value="">
						<button class="btn btn-primary btn-block">
							Search <i class="fa fa-filter"></i>
						</button>

						<? if (request()->exists('filter.search') && request()->input('filter.search')
								|| request()->exists('filter.guardian') && request()->input('filter.guardian')): ?>
							<p class="tr f4">
								<a href="?">Clear search</a>
							</p>
						<? endif; ?>
					</div>
				</form>
			</div>

		</div>

		<child-detail-pane ref="child-detail-pane"
			class="pxa pint pinr pinb w-40 bg-white shadow-2 flex flex-column justify-between"
			:orders="<?= e($DraftOrders->toJson()); ?>"
		></child-detail-pane>

	</div>

@stop

@section('js')
<script type="text/javascript">
(function($) {
	let current_child_id = null;

	$(function() {
		$('.child-list ').on('click', '.view-child-detail',  function(evt) {
			var $target = $(evt.target);

			if (!$target.hasClass('view-child-detail')) {
				$target = $target.parents('.view-child-detail:first');
			}

			var id = $target.data('id');

			showChildDetail($target, id);
		});

		$('.add-child-btn').on('click', function(evt) {
			createNewChild();
		});

		$(document).on('keyup', function(evt) {
			if (evt.keyCode == 27) {
				hideChildDetail();
			}
		});

		window.app.$refs['child-detail-pane'].$on('closed', detailsHidden);
		window.app.$refs['child-detail-pane'].$on('loaded', childLoaded);
		window.app.$refs['child-detail-pane'].$on('save', childSaved);
		window.app.$refs['child-detail-pane'].$on('delete', childDeleted);
		window.app.$refs['guardian-select'].$on('selected', guardianSelected)
	});

	function showChildDetail($targetRow, child_id) {
		deselectChildren();
		toggleSelected($targetRow);
		current_child_id = child_id;
		window.app.$refs['child-detail-pane'].$emit('view', child_id);
	}

	function createNewChild() {
		deselectChildren();
		window.app.$refs['child-detail-pane'].$emit('create');
	}

	function hideChildDetail() {
		window.app.$refs['child-detail-pane'].$emit('hide');
	}

	function childLoaded(id) {
		if (current_child_id != id) {
			deselectChildren();
			if (toggleSelected($('.view-child-detail[data-id=' + id + ']'))) {
				current_child_id = id;
			}
		}
	}

	function childSaved(Child) {
		window.app.$refs['child-list'].$emit('saved', Child);
		setTimeout(function() {
			childLoaded(Child.id);
			window.app.$refs['child-detail-pane'].$emit('view', Child.id);
		}, 1);
	}

	function childDeleted(Child) {
		window.app.$refs['child-list'].$emit('deleted', Child);
	}

	function detailsHidden(child_id) {
		deselectChildren();
	}

	function deselectChildren() {
		toggleSelected($('.view-child-detail'), false)
	}

	function toggleSelected($row, selected) {
		$row.toggleClass('bl blw3 bl--blue', selected !== false? true : selected)

		return $row.length > 0;
	}

	function guardianSelected(id, all) {
		$('#guardian_filter').val( all.join(',') );
	}
})(jQuery);
</script>
@stop