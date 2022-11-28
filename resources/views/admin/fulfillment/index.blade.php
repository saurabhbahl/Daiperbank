<? use App\Order; ?>

@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Order Fulfillment</p>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg-no bg-washed-blue">
				<ul class="nav nav-tabs ph3 pt3">
					<li class="dropdown bg-white active clickable:important">
						<a href="#" class="dropdown-toggle clickable:important" data-toggle="dropdown">
							Pickup Date:
							<? if ($PickupDate): ?>
								<?= $PickupDate->pickup_date->format('M j, Y'); ?>
								<span class="caret"></span>
							<? else: ?>
								<span class="i">None</span>
							<? endif; ?>
						</a>

						<ul class="dropdown-menu">
							<? foreach ($PickupDates as $Date): ?>
								<li class="<?= $PickupDate && $Date->id == $PickupDate->id? 'bg-black-025' : ''; ?>">
									<a href="?date=<?= $Date->pickup_date->format('Y-m-d'); ?>">
										<?= $Date->pickup_date->format('M j, Y'); ?>
										<? if ($PickupDate && $Date->id == $PickupDate->id): ?>
											<i class="ml fa fa-check"></i>
										<? endif; ?>
									</a>
								</li>
							<? endforeach ;?>
						</ul>
					</li>

					<li class="clickable:important">
						<a href="<?= route('admin.fulfillment.exported'); ?>" class="clickable:important">
							Upcoming Pickups
						</a>
					</li>
				</ul>
			</div>

			<div class="fg fs oy-auto">
				<? if ( ! $PickupDate): ?>
					<p class="tc f2 wtl pa4">
						There are no pickups scheduled with outstanding orders.
					</p>
				<? elseif ( ! $Orders || $Orders->count() == 0): ?>
					<p class="tc f2 wtl pa4">
						There are no orders pending export for this pickup date.
					</p>
				<? else: ?>
					<? foreach ($Orders as $Order): ?>
						<a class="db bb b--black-20 bw2 a:hov-nou a-plain hov:bg-washed-blue"
							href="<?= route('admin.order.view', [ $Order->id ]); ?>">
							<div class="pa pr5 flex justify-start items-center content-center">

								<div class="fg-no fs-no w-20 mr4">
									<p class="muted bb b--black-20">Order Number</p>
									<p class="wtl f2 pr5">
										#<?= e($Order->full_id); ?>
									</p>
								</div>

								<div class="mr4 w-30 fs-no">
									<p class="muted bb b--black-20">Agency</p>
									<p class="wtl f2 pr5">
										<?= e($Order->Agency->name); ?>
									</p>
								</div>


								<div class="w-20 fg-no mr4 nowrap">
									<table>
										<tr>
											<th class="tr" scope="row">Submitted on:</th>
											<td class="ph3 pv1">
												<?= e($Order->created_at->format('M j, Y')); ?>
											</th>
										</tr>
										<tr>
											<th class="tr" scope="row">Pickup on:</th>
											<td class="ph3 pv1">
												<?= e($Order->pickup_on->format('M j, Y')); ?>
											</th>
										</tr>
									</table>
								</div>


								<div class="fg-no fs-no w-10 mr4">
									<table>
										<tr>
											<td class="tr ph3 pv1"><?= e($Order->summary->children); ?></td>
											<th scope="row">Children</th>
										</tr>
										<tr>
											<td class="tr ph3 pv1"><?= e($Order->summary->diapers); ?></td>
											<th scope="row">Diapers</th>
										</tr>
										<tr>
											<td class="tr ph3 pv1"><?= ($Order->summary->pullups); ?></td>
											<th scope="row">Pull-ups</th>
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
		</div>

		<div class="col-xs-4 pa0 bg-white flex flex-column justify-between">
			<div class="pa o-hidden oy-auto">
				<div class="pt px4">
					<p class="f1 wtl lh1">
						<?= $Orders? number_format($Orders->count(), 0) : 0; ?>
						<span class="f3 light-muted wtn">Order<?= !$Orders || $Orders->count() != 1? 's' : ''; ?></span>
					</p>
					<p class="f3 wtb">Ready for Fulfillment</p>

					<div class="pt flex justify-start">
						<div class="w-50 mr4">
							<p class="f1 lh1 wtl">
								<?= $FilledOrders? number_format($FilledOrders->count(), 0) : 0; ?>
								<span class="f3 light-muted wtn">Order<?= !$FilledOrders || $FilledOrders->count() != 1? 's' : ''; ?></span>
							</p>
							<p class="f3 lh3 wtb">Filled</p>
						</div>
					</div>

					<? if ($PreviousBatches): ?>
						<p class="f1 wtl 1h1 pt4">
							<?= $PreviousBatches->count(); ?>
							<span class="f3 light-muted wtn">Previous Export<?= !$PreviousBatches || $PreviousBatches->count() != 1? 's' : ''; ?></span>
						</p>
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th>Export date</th>
									<th class="tr"># Orders</th>
								</tr>
							</thead>
							<tbody>
								<? if ($PreviousBatches->count()): ?>
									<? foreach ($PreviousBatches as $Batch): ?>
										<tr>
											<td><?= $Batch->created_at->format('M j, Y'); ?></td>
											<td class="tr"><?= number_format($Batch->Order->count(), 0); ?></td>
										</tr>
									<? endforeach; ?>
								<? else: ?>
									<tr>
										<td colspan="2" class="tc i">
											There have been no previous exports for this pickup date.
										</td>
									</tr>
								<? endif; ?>
							</tbody>
						</table>
					<? endif; ?>
				</div>
			</div>

			<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">
				<?= Form::open(['method' => 'post']); ?>
					<? if ($PickupDate): ?>
						<input type="hidden" name="date" value="<?= $PickupDate->pickup_date->format('Y-m-d'); ?>">
					<? endif; ?>

					<button
						class="btn btn-block btn-lg btn-primary <?= ! $PickupDate? 'disabled' : ''; ?> export-btn">
						<i class="fa fa-upload"></i>
						Export for Packing
					</button>
				<?= Form::close(); ?>
			</div>
		</div>
	</div>

@stop

@section('js')
<script type="text/javascript">
(function($) {
	$('.export-btn').on('click', function(evt) {
		var $el = $(this);

		if ($el.hasClass('disabled')) {
			evt.preventDefault();
			return false;
		}

		$(this).toggleClass('disabled', true);
		return true;
	});
})(jQuery);
</script>
@stop