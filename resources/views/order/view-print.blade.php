@extends('layouts.printed-order')

@section('order-id')
<?= e($Order->id); ?>
@stop

@section('content')
<div class="pa0 bg-white br b--black-20">
	<div class="flex flex-column">
		<div class="py flex justify-start items-start fg-no fs-no bg-washed-blue">
			<div class="mr4">
				<p class="muted bb b--black-20">Order Number</p>
				<p class="b f2 pr5">
					#{{ $Order->full_id }}
				</p>
			</div>

			<div class="mr4">
				<p class="muted bb b--black-20">Order Status</p>
				<p class="b f2 pr5" :class="orderStatusClasses()">
					{{ $Order->readable_status }}
				</p>
			</div>

			<div class="mr4">
				<p class="muted bb b--black-20">Pickup Date</p>
				<p class="b f2 pr5" :class="orderStatusClasses()">
					{{ $Order->PickupDate->pickup_date->format('M d, Y @ h:ma') }}
				</p>
			</div>
		</div>

		<div class="clearfix">
			<div class="w-60 fl">
				<? foreach ($Children as $Child): ?>
					<div class="">
						<span class="f2">
							<i class="fa <?= $Child->gender == 'f'? 'fa-female' : 'fa-male'; ?>"></i>
							{{ $Child->name }}
						</span>

						<table class="table table-condensed ma0">
							<thead>
								<tr>
									<th class="w-15">Age</th>
									<th class="w-15">Type</th>
									<th class="w-15">Size</th>
									<th class="w-10">Qty</th>
									<th class="w-15">Weight</th>
									<th>Potty Training</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>{{ $Child->age_str }}</td>

									<td>{{ $Child->Item->Product->Category->name }}</td>

									<td>{{ $Child->Item->Product->name }}</td>

									<td>{{ $Child->Item->quantity }}</td>

									<td>{{ $Child->weight_str }}</td>
									<td>
										<? if ($Child->status_potty_train): ?>
											<i class="fa fa-check dark-green"></i>
											Yes
										<? else: ?>
											<i class="fa fa-times dark-red"></i>
											No
										<? endif; ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				<? endforeach; ?>
			</div>
			<div class="w-30 fr">
				<p class="f2 b mv3">Order Summary</p>

				<table class="table table-condensed table-striped">
					<thead>
						<tr>
							<th>Size</th>
							<th class="w-25 tc">Order Qty</th>
						</tr>
					</thead>

					<tbody>
						<? foreach ($ProductSummary as $Product): if ( ! $Product->Inventory || ! $Product->Inventory->ordered) continue; ?>
							<tr>
								<th scope="row">
									<p class="ma0 pa0 wtn">{{ $Product->name }}</p>
									<p class="f4 ma0 pa0 black-50">{{ $Product->Category->name }}</p>
								</th>
								<td class="tc">
									{{ $Product->Inventory->ordered }}
								</td>
							</tr>
						<? endforeach ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
@stop

@section('js')
<script type="text/javascript">
(function() {
	window.print();
})();
</script>