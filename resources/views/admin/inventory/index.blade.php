@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">Inventory</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg fs pa4 oy-auto">
				<div>
					<div class="flex justify-between bb bw2 b--light-gray">
						<p class="w-10 fg-no fs-no "></p>
						<p class="b fg">Details</p>
						<p class="b w-20 fg-no fs-no tr pr5">Date</p>
					</div>

					<? foreach ($Adjustments as $Adjustment): ?>
						<inventory-adjustment-table
							:adjustment='<?= e($Adjustment->toJson()); ?>'
							:key='<?= $Adjustment->id; ?>'
						></inventory-adjustment-table>
					<? endforeach; ?>
				</div>
			</div>

			<? if ($Adjustments->lastPage() > 1): ?>
				<div class="fg-no fs-no pv3 flex justify-center bg-washed-blue bt b--black-20">
					<a href="<?= $Adjustments->previousPageUrl() ?? "#"; ?>"
						class="btn btn-default mr <?= ! $Adjustments->previousPageUrl()? 'mod-disabled' : ''; ?>">
						<i class="fa fa-chevron-left"></i>
						Previous Page
					</a>

					<? $start_page = max($Adjustments->currentPage() - 2, 1); ?>
					<? $end_page = min($start_page + 4, $Adjustments->lastPage()); ?>
					<? foreach (range($start_page, $end_page) as $pg): ?>
						<a href="<?= $Adjustments->url( $pg ); ?>"
							class="btn btn-default mr <?= $pg == $Adjustments->currentPage()? 'b' : ''; ?>">
							<?= $pg; ?>
						</a>
					<? endforeach; ?>

					<a href="<?= $Adjustments->nextPageUrl() ?? "#"; ?>"
						class="btn btn-default <?= ! $Adjustments->nextPageUrl()? 'mod-disabled' : ''; ?>">
						Next Page
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
			<? endif; ?>
		</div>

		<div class="col-xs-4 pb flex flex-column justify-start oy-auto bg-white">
			<div class="mv4 bg-white br3 py4 px4">
				<form method="get">
					<div class="mb">
						<p class="b">Search</p>
						<input name="search" type="search" class="form-control"
							placeholder="Search for Inventory..."
							value="<?= e(request()->input('search')) ?>" />
						<p class="tr"><small><a href="#">Search tips</a></small></p>
					</div>

					<div class="pt">
						<input type="hidden" name="page" value="">
						<button class="btn btn-primary btn-block">
							Search <i class="fa fa-filter"></i>
						</button>

						<? if (request()->exists('filter.search') && request()->input('filter.search')): ?>
						<p class="tr f4">
							<a href="?">Clear search</a>
						</p>
						<? endif; ?>
					</div>
				</form>
			</div>
			<div class="mv4 bb b--light-gray pb4">
				<a href="<?= route('admin.inventory.create_adjustment'); ?>"
					class="btn btn-block btn-lg btn-default">
					Create Adjustment
				</a>
			</div>

			<div class="mv">
				<table class="table table-condensed table-striped mb">
					<thead>
						<tr>
							<th></th>
							<th>Diapers</th>
							<th>Pull-ups</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope="row">
								On-hand
							</th>
							<td>
								<?= number_format($Summary->onHand->Diapers, 0); ?>
							</td>
							<td>
								<?= number_format($Summary->onHand->Pullups, 0); ?>
							</td>
						</tr>
						<tr>
							<th scope="row">
								Pending Approval
							</th>
							<td>
								<?= number_format($Summary->pendingApproval->Diapers, 0); ?>
							</td>
							<td>
								<?= number_format($Summary->pendingApproval->Pullups, 0); ?>
							</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<th scope="row" class="bw2">
								Projected
							</th>
							<td class="bw2">
								<?= number_format($Summary->onHand->Diapers - $Summary->pendingApproval->Diapers, 0); ?>
							</td>
							<td class="bw2">
								<?= number_format($Summary->onHand->Pullups - $Summary->pendingApproval->Pullups, 0); ?>
							</td>
						</tr>
					</tfoot>
				</table>
			</div>

			<div class="mv">
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