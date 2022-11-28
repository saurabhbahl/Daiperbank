<div class="summary-header">
	<div class="container">
		<div class="row">
			<div class="col-md-4 summary-header__component">
				<h1><?= number_format($Summary->pendingApproval, 0); ?></h1>
				<strong>New Orders</strong>
			</div>

			<div class="col-md-4 summary-header__component">
				<h1><?= number_format($Summary->pendingPickup, 0); ?></h1>
				<strong>Orders Pending Pickup</strong>
			</div>

			<div class="col-md-4 summary-header__component">
				<h1>555</h1>
				<strong>Some useful stat</strong>
			</div>
		</div>
	</div>
</div>