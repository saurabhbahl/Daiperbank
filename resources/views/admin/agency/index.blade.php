<? use App\Agency; ?>

@extends('layouts.app')

@section('content')

	<div class="breadcrumbs">
		<p class="crumb">Agencies</p>
	</div>
<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg-no bg-washed-blue">
				<ul class="nav nav-tabs ph3 pt3">
					<li class="dropdown bg-white active clickable:important">
						<a href="#" class="dropdown-toggle clickable:important" data-toggle="dropdown">
							<?= e($selected_order_status?
									( $selected_order_status == 'all'
										? 'All Status'
										: Agency::agencyStatusText($selected_order_status)
									) : 'Search results'
							); ?>
							<span class="caret"></span>
						</a>

						<ul class="dropdown-menu">
							<? foreach (Agency::getStatuses() as $key => $value): ?>
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
									Show all Status
									<? if ('all' == $selected_order_status): ?>
										<i class="ml fa fa-check"></i>
									<? endif; ?>
								</a>
							</li>
						</ul>
					</li>
				</ul>
			</div>
	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg fs oy-auto">
				<? foreach ($Agencies as $Agency): ?>

					<div class="bb b--black-20 flex justify-start">
						<div class="pa w-25">
							<p class="b"  >
								<b-button v-b-tooltip.hover title='<?= e($Agency->name ); ?>'>
								<?= e($Agency->id_prefix . '-' . $Agency->id); ?>
								</b-button>							
								
							</p>

							
							<p class="wtn f4 muted">
								<? if ($Agency->id_prefix): ?>
									<i class="f5 fa fa-hashtag"></i>
									<?= $Agency->id_prefix; ?>
								<? endif; ?>
							</p>

							<p>
								<?= e($Agency->address); ?>
								<br>
								<? if (!empty($Agency->address_2)): ?>
									<?= e($Agency->address_2); ?>
									<br>
								<? endif; ?>
								<?= e($Agency->city . ' ' . $Agency->state . ', ' . $Agency->zip); ?>
							</p>
						</div>

						<div class="pa w-25">
							<p class="b">Contact:</p>
							<? $Contact = $Agency->Contact->first(); ?>

							<? if ($Contact): ?>
								<p><?= e($Contact->name); ?></p>

								<? if ($Contact->email): ?>
									<p>
										<i class="fa fa-envelope"></i>
										<?= e($Contact->email); ?>
									</p>
								<? endif; ?>

								<? if ($Contact->phone): ?>
									<p>
										<i class="fa fa-phone"></i>
										<?= e(phone_format($Contact->phone)); ?>
										<? if ($Contact->phone_extension): ?>
											x<?= e($Contact->phone_extension); ?>
										<? endif; ?>
									</p>
								<? endif; ?>
							<? else: ?>
								<p class="i muted f4">No contact information</p>
							<? endif; ?>
						</div>

						<div class="pa flex-auto w-25">
							<p class="b">Login Username:</p>
							<p>
								<?= e($Agency->User->username); ?>
							</p>

						</div>

						<div class="pa flex-auto tc w-15">
							<? if ($Agency->agency_status == Agency::STATUS_ACTIVE): ?>
								<a href="<?= route('admin.agency.edit', [ $Agency->id ]); ?>"
									class="btn btn-primary btn-block">
									<i class="fa fa-pencil"></i>
									Edit
								</a>

								<div class="flex flex-auto mt3 justify-center">
									<? if ($Agency->id != Auth()->User()->Agency->id): ?>
										<?= Form::open(['method' => 'post', 'route' => ['admin.agency.act_as', $Agency->id]]); ?>
											<a href="#"
												onclick="this.parentElement.submit();"
												class="btn btn-default btn-ghost btn-xs mr">

												<i class="fa fa-user-o"></i>
												Log in
											</a>
										<?= Form::close(); ?>

										<?= Form::open(['method' => 'post', 'route' => ['admin.agency.status', $Agency->id]]); ?>
											<input type="hidden" name="agency_status" value="<?= Agency::STATUS_INACTIVE; ?>">
											<a href="#" onclick="this.parentElement.submit();"
												class="btn btn-danger btn-ghost btn-xs">

												<i class="fa fa-times"></i>
												Disable
											</a>
										<?= Form::close(); ?>
									<? endif; ?>
								</div>
							<? else: ?>
								<?= Form::open(['method' => 'post', 'route' => ['admin.agency.status', $Agency->id]]); ?>
									<input type="hidden" name="agency_status" value="<?= Agency::STATUS_ACTIVE; ?>">
									<a href="#" onclick="this.parentElement.submit();"
										class="btn btn-block btn-default">
										<i class="fa fa-power-off"></i>
										Enable
									</a>
								<?= Form::close(); ?>
							<? endif; ?>
						</div>
					</div>
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
				<a href="<?= route('admin.agency.create'); ?>" class="btn btn-block btn-default">New Agency</a>
			</div>

			<div class="mv4 bg-white br3 py4 px4">
				<form method="get">
					<div class="mb">
						<p class="b">Search</p>
						<input name="filter[search]"
							type="search"
							class="form-control"
							placeholder="Search for agencies..."
							value="<?= e(request()->input('filter.search')); ?>"
						/>
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
		</div>
	</div>
@stop