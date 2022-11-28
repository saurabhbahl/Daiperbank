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
						<? foreach ($ProductInventory as $Product): ?>
							<?
							$classname = null;
							if ($Product->Inventory && inventoryWarning($Product, $Product->Inventory->on_hand)) $classname = 'inventory-warning';
							if ($Product->Inventory && inventoryCritical($Product, $Product->Inventory->on_hand)) $classname = 'inventory-critical';
							?>
							<tr class="<?= $classname; ?>">
								<th scope="row">
									<p class="wtn"><?= e($Product->name); ?></p>
									<p class="f4 light-muted"><?= e($Product->Category->name); ?></p>
								</th>
								<td class="tc br bl b--black-10 inventory-on-hand">
									<? if ($Product->Inventory): ?>
										<p><?= number_format($Product->Inventory->on_hand, 0); ?></p>
									<? else: ?>
										<p class="light-muted">&mdash;</p>
									<? endif; ?>
								</td>
								<td class="tc">
									<? if ($Product->Inventory && $Product->Inventory->pending_approval): ?>
										<p><?= number_format($Product->Inventory->pending_approval, 0) ?: '&mdash;'; ?></p>
									<? else: ?>
										<p class="light-muted">&mdash;</p>
									<? endif; ?>
								</td>
							</tr>
						<? endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
@stop