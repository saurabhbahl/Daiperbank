@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb crumb--main">Agencies</p>
		<p class="separator">/</p>
		<p class="crumb">
			<?= e($Agency->name); ?>
		</p>

		<br>

		<a href="<?=route('admin.agency.index');?>">Back</a>
	</div>

	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 pa0 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden">

			<div class="fs-no fg-no pt3">
				<ul class="nav nav-tabs ph3" role="tablist">
					<li role="presentation" class="active">
						<a href="#details" aria-controls="home" role="tab" data-toggle="tab">Details</a>
					</li>
					<li role="presentation">
						<a href="#children" aria-controls="profile" role="tab" data-toggle="tab">Children</a>
					</li>
				</ul>
			</div>

			<div class="fg fs oy-auto tab-content">
				<div role="tabpanel" class="pa4 tab-pane active clearfix" id="details">
					<div class="col-xs-6 pa0 pr4">
						<p class="pb">
							<label for="name" class="b">Agency name:</label>
							<? if (!$editing): ?>
								<span><?= e($Agency->name); ?></span>
							<? else: ?>
								<input type="text" name="name" id="name"
									class="form-control" value="<?= e(old('name', $Agency->name)); ?>">
							<? endif; ?>
						</p>

						<p class="pb">
							<span class="b">ID Prefix:</span>
							<span><?= e($Agency->id_prefix); ?></span>
						</p>

						<div class="pb">
							<label for="address" class="b pb2 db">Address:</label>

							<? if (!$editing): ?>
								<p class="ml3 pl3 py0 bl b--black-10 bw2">
									<?= e($Agency->address); ?>
									<br>
									<? if ($Agency->address_2): ?>
										<?= e($Agency->address_2); ?>
										<br>
									<? endif; ?>
									<?= e($Agency->city); ?>,
									<?= e($Agency->state); ?> <?= e($Agency->zip); ?>
								</p>
							<? else: ?>
								<input type="text" name="address" id="address"
									class="form-control" value="<?= e(old('address', $Agency->address)); ?>">

								<input type="text" name="address_2" id="address_2"
									class="form-control" value="<?= e(old('address_2', $Agency->address_2)); ?>">

								<div class="flex justify-start">
									<div>
										<label for="city">City:</label>
										<input type="text" name="city" id="city"
											class="form-control" value="<?= e(old('city', $Agency->city)); ?>">
									</div>

									<div>
										<label for="state">State:</label>
										<input type="text" name="city" id="city"
											class="form-control" value="<?= e(old('city', $Agency->city)); ?>">
									</div>

									<div>
										<label for="city">City:</label>
										<input type="text" name="city" id="city"
											class="form-control" value="<?= e(old('city', $Agency->city)); ?>">
									</div>

							<? endif;>
						</div>

						<div class="pb">
							<p class="b pb2">Contact:</p>

							<p class="ml3 pl3 py0 bl b--black-10 bw2">
								<? if ( ! $Contact = $Agency->Contact()->first()): ?>
									<p class="i">No contact configured for this agency.</p>
								<? else: ?>
									<?= e($Contact->name); ?>
									<br>

									<? if ($Contact->email): ?>
										<i class="fa fa-envelope"></i>
										<a href="mailto:<?= e($Contact->email); ?>"><?= e($Contact->email); ?></a>
										<br>
									<? endif; ?>

									<? if ($Contact->phone): ?>
										<i class="fa fa-phone"></i>
										<?= phone_format($Contact->phone); ?>

										<? if ($Contact->phone_extension): ?>
											x<?= e($Contact->phone_extension); ?>
										<? endif; ?>
									<? endif; ?>
								<? endif; ?>
							</p>
						</div>
					</div>

					<div class="col-xs-6 pa0">
						<div class="pb">
							<p class="b pb2">Login Credentials:</p>

							<div class="ml3 pl3 py0 bl b--black-10 bw2">
								<p>
									<span class="b">Username:</span>
									<?= e($Agency->User()->first()->username); ?>
								</p>
								<p>
									<span class="b">Password:</span>
									<a href="#" class="btn btn-xs btn-default">Send reset instructions</a>
								</p>
							</div>
						</div>
					</div>
				</div>

				<div role="tabpanel" class="tab-pane" id="children">
					<? foreach ($Agency->Children as $Child): ?>
						<div class="pa4 bb b--black-20">
							<div class="flex">
								<p class="w-30 fg-no fs-no">
									<i class="fa fa-<?= $Child->gender == 'f'? 'fe' : ''; ?>male"></i>
									<?= e($Child->name); ?>
									<br>
									<span class="muted f4 db">
										<i class="fa f5 fa-hashtag"></i>
										<?= $Child->uniq_id; ?>
									</span>
								</p>

								<p class="ml3">
									<span class="b">DOB:</span>
									<?= $Child->dob->format('m-d-Y'); ?> (<?= $Child->age_str; ?>)
									<br>
									<span class="b">County:</span>
									<?= $Child->Location->first()->county ?? 'N/A'; ?>
								</p>
							</div>
						</div>
					<? endforeach; ?>
				</div>
			</div>
		</div>

		<div class="col-xs-4 pa0 bg-white flex flex-column justify-between">
			<div class="pa4 o-hidden oy-auto">
				<div>
					<p>77</p>
					<p>Orders</p>
				</div>

				<div>
					<p><?= e(number_format($Agency->Children()->count(), 0)); ?></p>
					<p>Children</p>
				</div>

				<div>
					<p>10</p>
					<p>Families</p>
				</div>
			</div>

			<div class="flex fs-no fg-no justify-between content-between pa4 bt b--black-20">
				<a class="w-80 btn btn-lg btn-block btn-success fs mr2"
					href="?edit">
					Edit
					<i class="fa fa-lg fa-edit"></i>
				</a>
				<div class="dropup w-10 fg self-end tc">
					<button class="btn btn-lg btn-default btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fa fa-lg fa-caret-up"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="#">Send message</a></li>
						<li><a href="#">View Orders</a></li>
						<li><a href="#">Disable Application Access</a></li>
						<li role="separator" class="divider"></li>
						<li><a href="#">Remove Agency</a></li>
						<li><a href="#">Log-in as Agency</a></li>
					</ul>
				</div>
			</div>
		</div>
	</div>
@stop

