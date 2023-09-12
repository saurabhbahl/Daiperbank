<a class="db bb b--black-20 a:hov-nou a-plain hov:bg-washed-blue"
	href="<?= ! $Order->isDraft()? route('order.view', [ $Order->id ]) : route('order.create', [ $Order ]); ?>">

	<div class="pa pr5 flex justify-start items-center content-center">
		<div class="fg-no fs-no w-20 mr4">
			<p class="muted bb b--black-20">Order Number</p>
			<p class="wtl f2 pr5">
				#<?= $Order->full_id; ?>
			</p>
		</div>

		<div class="w-20 fg-no mr4 nowrap">
			<table>
				<tr>
					<th class="tr" scope="row">
						<? if ( ! $Order->isDraft()): ?>
							Submitted on:
						<? else: ?>
							Started on:
						<? endif; ?>
					</th>
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
							TBD
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

<? return; ?>
	<div class="pa pr5 flex justify-start items-center content-center">
		<div class="fg-no fs-no w-20 mr4">
			<p class="muted bb b--black-20">Order Number</p>
			<p class="b f2 pr5">
				#<?= $Order->full_id; ?>
			</p>
		</div>

		<div class="w-25 fg-no mr4">
			<table>
				<tr>
					<th class="tr" scope="row">Submitted on:</th>
					<td class="ph3 pv1">
						<?= $Order->created_at->format('M j, Y'); ?>
					</td>
				</tr>
				<tr>
					<th class="tr" scope="row">Pickup on:</th>
					<td class="ph3 pv1">
						<?= $Order->pickup_on->format('M j, Y'); ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="fg-no fs-no w-25 mr4">
			<table>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->children; ?></td>
					<th scope="row">Children</th>
				</tr>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->diapers; ?></td>
					<th scope="row">Diapers</th>
				</tr>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->pullups; ?></td>
					<th scope="row">Pull-ups</th>
				</tr>
			</table>
		</div>

		<div class="mlauto w-20 mr4">
			<p class="muted bb b--black-20">Order Status</p>
			<p class="b f2 pr5">
				<?= e($Order->readable_status); ?>
			</p>
		</div>

		<div class="mlauto fg-no fs-no">
			<i class="fa fa-chevron-right"></i>
		</div>
	</div>
</a>

<? return; ?>

	<a class="db bb b--black-20 a:hov-nou a-plain hov:bg-washed-blue"
	href="<?= route('order.view', [ $Order->id ]); ?>">

	<div class="pa pr5 flex justify-between items-center content-center">
		<div class="fg-no fs-no w-20">
			<p>
				<span class="muted">#</span><span class="b"><?= $Order->full_id; ?></span>
				<br>
				<span class="f4 muted">Order Number</span>
			</p>
		</div>

		<div class="w-25 fg-no">

			<table>
				<tr>
					<th class="tr" scope="row">Submitted on:</th>
					<td class="ph3 pv1">
						<?= $Order->created_at->format('M j, Y'); ?>
					</td>
				</tr>
				<tr>
					<th class="tr" scope="row">Pickup on:</th>
					<td class="ph3 pv1">
						<?= $Order->pickup_on->format('g:ia'); ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="fg-no fs-no w-20">
			<table>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->children; ?></td>
					<th scope="row">Children</th>
				</tr>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->diapers; ?></td>
					<th scope="row">Diapers</th>
				</tr>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->pullups; ?></td>
					<th scope="row">Pull-ups</th>
				</tr>
			</table>
		</div>

		<div class="w-20">

			<p class="tc b">
				<?= e($Order->readableStatus()); ?>
			</p>
			<p class="f4 tc muted">Current Status</p>
		</div>
	</div>
</a>

	<div class="pv4 ph3 flex justify-start items-start">
		<div class="mr4">
			<p class="muted bb b--black-20">Order ID</p>
			<p class="b f2 pr5">
				#<?= $Order->full_id; ?>
			</p>
		</div>

		<div class="mr4 w-25">
			<table>
				<tr>
					<th class="tr" scope="row">Submitted on:</th>
					<td class="ph3 pv1">
						<?= $Order->created_at->format('M j, Y'); ?>
					</td>
				</tr>
				<tr>
					<th class="tr" scope="row">Pickup on:</th>
					<td class="ph3 pv1">
						<?= $Order->pickup_on->format('g:ia'); ?>
					</td>
				</tr>
			</table>
		</div>

		<div class="mr4 w-25">
			<table>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->children; ?></td>
					<th scope="row">Children</th>
				</tr>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->diapers; ?></td>
					<th scope="row">Diapers</th>
				</tr>
				<tr>
					<td class="tr ph3 pv1"><?= $Order->summary->pullups; ?></td>
					<th scope="row">Pull-ups</th>
				</tr>
			</table>
		</div>

		<div class="mlauto w-15 self-end">
			<p class="muted bb b--black-20">Order Status</p>
			<p class="b f2 pr5">
				<?= e($Order->readable_status); ?>
			</p>
		</div>
	</div>
</a>

<? return; ?>
