
@section('trash')
<div class="pa pr5 flex justify-between items-center content-center">
								<div class="fg-no fs-no w-30">
									<p class="f2"><?= e($Order->Agency->name); ?></p>

									<p>
										<span class="f3">#</span><span class="f2"><?= $Order->full_id; ?></span>
									</p>
								</div>

								<div class="fg-no fs-no w-30">
									<p><?= $Order->summary->children; ?>
									Children</p>

									<p><?= $Order->summary->diapers; ?>
									Diapers</p>

									<p><?= $Order->summary->pullups; ?>
									Pull-ups</p>
								</div>

								<div class="fg-no fs-no w-30">
									<p class="">
										<?= e($Order->readableStatus()); ?>
									</p>

									<p>
										<i class="fa fa-calendar-o fa-stack-lg"></i>
										<?= $Order->pickup_on->format('M j, Y @ h:ia'); ?>
									</p>
								</div>

								<div class="fg-no fs-no pull-right">
									<i class="fa fa-2xl fa-chevron-right"></i>
								</div>
							</div>
							@stop