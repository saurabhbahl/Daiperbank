<? use App\Order; ?>

@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Orders</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg-no bg-washed-blue">
				<ul class="nav nav-tabs ph3 pt3">
					<li class="dropdown bg-white active clickable:important">
						<a href="#" class="dropdown-toggle clickable:important" data-toggle="dropdown">
							<?= e($selected_order_status?
									( $selected_order_status == 'all'
										? 'All Orders'
										: Order::orderStatusText($selected_order_status)
									) : 'Search results'
							); ?>
							<span class="caret"></span>
						</a>

						<ul class="dropdown-menu">
							<? foreach (Order::getStatuses() as $key => $value): ?>
								<li class="<?= $key == $selected_order_status? 'bg-black-025' : ''; ?>">
									<a href="?status=<?= $key; ?>">
										<?= e($value); ?>
										<? if ($key == $selected_order_status): ?>
											<i class="ml fa fa-check"></i>
										<? endif; ?>
									</a>
								</li>
							<? endforeach ;?>
							<li class="divider"></li>
							<li class="<?= 'all' == $selected_order_status? 'bg-black-025' : ''; ?>">
								<a href="?status=all">
									Show all orders
									<? if ('all' == $selected_order_status): ?>
										<i class="ml fa fa-check"></i>
									<? endif; ?>
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>

			<div class="fg fs oy-auto">
				<? if ($Orders->count() == 0): ?>
					<p class="tc f2 wtl pa4">
						<? if (request()->has('filter')): ?>
							Couldn't find any orders that match your search criteria.
						<? elseif ('all' != $selected_order_status && null != $selected_order_status): ?>
							No orders found with status '<?= Order::orderStatusText( $selected_order_status ); ?>'
						<? else: ?>
							No orders found
						<? endif; ?>
					</p>
				<? else: ?>
					<? foreach ($Orders as $Order): ?>
						<a class="db bb b--black-20 bw2 a:hov-nou a-plain hov:bg-washed-blue"
							href="<?= route('admin.order.view', [ $Order->id ]); ?>">
							<div class="pa pr5 flex justify-start items-center content-center">

								<div class="fg-no fs-no w-20 mr4">
									<p class="muted bb b--black-20">Order Number</p>
									<p class="wtl f2 pr5">
										#<?= $Order->full_id; ?>
									</p>
								</div>

								<div class="mr4 w-20 fs-no">
									<p class="muted bb b--black-20">Agency</p>
									<p class="wtl f2 pr5">
										<?= e($Order->Agency->name); ?>
									</p>
								</div>


								<div class="w-20 fg-no mr4 nowrap">
									<table>
										<tr>
											<th class="tr" scope="row">Order created:</th>
											<td class="ph3 pv1">
												<?= $Order->created_at->format('M j, Y'); ?>
											</th>
										</tr>
										<tr>
											<th class="tr" scope="row">Pickup on:</th>
											<td class="ph3 pv1">
												<? if ($Order->pickup_on): ?>
													<?= $Order->pickup_on->format('M j, Y'); ?>
												<? else: ?>
													<span class="em">TBD</span>
												<? endif; ?>
											</th>
										</tr>
									</table>
								</div>

								<? $child = 0; $menstruator = 0; $period_product = 0;
									foreach($Order->Child as $order_child){
										if($order_child->child->is_menstruator == 0){
											$child++;
										}
										else{
											$menstruator++;
										}
									}
									if($Order->Item->isNotEmpty()){
										foreach($Order->Item as $item){
											if($item->Product->Category->id == 3){
												$period_product	= $item->quantity;
											}
										}
									}
								?>
								<div class="fg-no fs-no w-10 mr4">
									<table>
										<tr>
											<td class="tr ph3 pv1"><?= $child; ?></td>
											<th scope="row">Children</th>
										</tr>
										<tr>
											<td class="tr ph3 pv1"><?= $menstruator; ?></td>
											<th scope="row">Menstruator</th>
										</tr>
										<tr>
											<td class="tr ph3 pv1"><?= $Order->summary->diapers; ?></td>
											<th scope="row">Diapers</th>
										</tr>
										<tr>
											<td class="tr ph3 pv1"><?= $Order->summary->pullups; ?></td>
											<th scope="row">Pull-ups</th>
										</tr>
										<tr>
											<td class="tr ph3 pv1"><?= $period_product; ?></td>
											<th scope="row">Period Products</th>
										</tr>
									</table>
								</div>

								<div class="mlauto w-10 fg-no mr4">
									<p class="muted bb b--black-20">Order Status</p>
									<p class="wtl f2 pr5">
										<?= e($Order->readable_status); ?>
									</p>
								</div>

								<div class="fg-no fs-no">
									<i class="fa fa-chevron-right"></i>
								</div>
							</div>
						</a>
					<? endforeach; ?>
				<? endif; ?>
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

			<div class="ma mv4 bg-white br3">
				<div class="pt px4">
					<p class="f1">Inventory</p>

					<div class="flex justify-end items-start mb3 tr">
						<div class="mr3">
							<p class="f2 lh1 wtl mb1">Total</p>
							<p class="f3 lh3 light-muted">On-hand</p>
						</div>

						<div class="mr3 tr">
							<p class="f2 lh1 wtl mb1"><?= number_format($InventorySummary->onHand->Diapers, 0); ?></p>
							<p class="f3 lh3 light-muted">Diapers</p>
						</div>

						<div class="tr">
							<p class="f2 lh1 wtl mb1"><?= number_format($InventorySummary->onHand->Pullups, 0); ?></p>
							<p class="f3 lh3 light-muted">Pull-ups</p>
						</div>
					</div>
				</div>

				<table class="table table-condensed table-striped mb">
					<thead>
						<tr>
							<th class="w-40">Size</th>
							<th class="w-20 tc br bl b--black-10">On-hand</th>
							<th class="w-40 tc">Pending Approval</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$uniqueProducts = [];
						foreach ($ProductInventory as $Product) {
							$productName = str_replace(['Boy', 'Girl'], '', $Product->name);

							// Check if the product name is already in the uniqueProducts array
							if (isset($uniqueProducts[$productName])) {
								// If it exists, add the "on-hand" quantity to the existing total
								$uniqueProducts[$productName]['on_hand'] += $Product->Inventory ? $Product->Inventory->on_hand : 0;
							} else {
								// If it doesn't exist, create a new entry
								$uniqueProducts[$productName] = [
									'name' => $productName,
									'category' => $Product->Category->name,
									'on_hand' => $Product->Inventory ? $Product->Inventory->on_hand : 0,
								];
							}

							// Determine the class name based on inventory warnings
							$classname = null;
							if ($Product->Inventory && inventoryWarning($Product, $Product->Inventory->on_hand)) {
								$classname = 'inventory-warning';
							}
							if ($Product->Inventory && inventoryCritical($Product, $Product->Inventory->on_hand)) {
								$classname = 'inventory-critical';
							}
						}

						// Output unique product rows with the total "on-hand" quantity
						foreach ($uniqueProducts as $uniqueProduct) {
							echo '<tr class="' . $classname . '">';
							echo '<th scope="row">';
							echo '<p class="wtn">' . e($uniqueProduct['name']) . '</p>';
							echo '<p class="f4 light-muted">' . e($uniqueProduct['category']) . '</p>';
							echo '</th>';
							echo '<td class="tc br bl b--black-10 inventory-on-hand">';
							echo '<p>' . number_format($uniqueProduct['on_hand'], 0) . '</p>';
							echo '</td>';
							echo '<td class="tc">';
							echo '<p class="light-muted">—</p>';
							echo '</td>';
							echo '</tr>';
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@stop
