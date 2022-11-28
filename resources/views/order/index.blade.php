<? use App\Order; ?>

@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Orders</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">

			<div class="fg fs oy-auto">
				<? foreach ($Orders as $Order): ?>
					@include('agency.partials.order.list-item', [ 'Order' => $Order ])
				<? endforeach; ?>
			</div>

			<? if ($Pagination->lastPage() > 1): ?>
				<div class="fg-no fs-no pv3 flex justify-center bg-washed-blue bt b--black-20">
					<a href="<?= $Pagination->previousPageUrl() ?? "#"; ?>"
						class="btn btn-default mr <?= ! $Pagination->previousPageUrl()? 'mod-disabled' : ''; ?>">
						<i class="fa fa-chevron-left"></i>
						Previous Page
					</a>

					<? $start_page = max($Pagination->currentPage() - 2, 1); ?>
					<? $end_page = min($start_page + 4, $Pagination->lastPage()); ?>
					<? foreach (range($start_page, $end_page) as $pg): ?>
						<a href="<?= $Pagination->url( $pg ); ?>"
							class="btn btn-default mr <?= $pg == $Pagination->currentPage()? 'b' : ''; ?>">
							<?= $pg; ?>
						</a>
					<? endforeach; ?>

					<a href="<?= $Pagination->nextPageUrl() ?? "#"; ?>"
						class="btn btn-default <?= ! $Pagination->nextPageUrl()? 'mod-disabled' : ''; ?>">
						Next Page
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			<? endif; ?>
		</div>

		<div class="col-xs-4 pb flex flex-column justify-start oy-auto">
			<div class="fs-no fg-no pv4 bb b--black-20">
				<?= Form::open(['method' => 'post', 'route' => ['home']]); ?>
					<button type="submit"
						name="action"
						value="create"
						class="btn btn-default btn-block btn-lg">
						<i class="fa fa-file-text-o"></i>
						Create New Order
					</button>
				<?= Form::close(); ?>
			</div>

			<div class="ma mv4 bg-white br3 py4 px4">
				<order-search-filter
					:show="<?= json_encode((bool) request()->has('filter.filter')); ?>"
					:filters-active="<?= json_encode((bool) request()->has('filter.filter')); ?>">
					<?= Form::open(['method' => 'get']); ?>
						<div class="mb">
							<p class="b">Search</p>
							<input name="filter[search]"
								type="search"
								class="form-control"
								placeholder="Search for orders..."
								value="<?= e(request()->input('filter.search')); ?>"
							/>
							<p class="tr"><small><a href="#">Search tips</a></small></p>
						</div>

						<div class="mb">
							<p class="b">Order Status</p>
							<select name="filter[status]" class="form-control">
								<option value="">All</option>
								<? foreach (Order::getStatuses() as $value => $label): ?>
									<option value="<?= e($value); ?>"
										<?= request()->input('filter.status') == $value? 'selected' : ''; ?>
										><?= e($label); ?></option>
								<? endforeach; ?>
							</select>
						</div>

						<div class="mb">
							<p class="b">Ordered After</p>
							<div class="input-group mb1">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								<input type="date" class="form-control"
									name="filter[start_date]"
									value="<?= e(request()->input('filter.start_date')); ?>">
							</div>
						</div>

						<div class="mb">
							<p class="b">Ordered Before</p>
							<div class="input-group">
								<span class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</span>
								<input type="date" class="form-control"
									name="filter[end_date]"
									value="<?= e(request()->input('filter.end_date')); ?>">
							</div>
						</div>

						<div class="pt">
							<input type="hidden" name="page" value="">
							<button name="filter[filter]" value="filter" class="btn btn-primary btn-block">
								Filter Orders <i class="fa fa-filter"></i>
							</button>
						</div>
					<?= Form::close(); ?>
				</order-search-filter>
			</div>

		</div>
	</div>
@stop

