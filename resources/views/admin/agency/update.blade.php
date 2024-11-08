@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb crumb--main">Agencies</p>
		<p class="separator">/</p>
		<p class="crumb">
			<? if ($Agency && $Agency->exists): ?>
				<?= e($Agency->name); ?>
				<? if ($Agency->id_prefix): ?>
					<span class="muted f4 wtn">
						<i class="fa f5 fa-hashtag"></i>
						<?= e($Agency->id_prefix); ?>
					</span>
				<? endif; ?>
			<? else: ?>
				New Agency
			<? endif; ?>
		</p>

		<br>

		<a href="<?=route('admin.agency.index');?>">Back</a>
	</div>

	<div class="flex-auto pxr">
		<?= Form::open(['method' => 'post', 'class' => 'pxa pf flex justify-start content-stretch o-hidden']); ?>
			<div class="col-xs-8 pa0 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden">
				<? /*
					-- Tabs for switching between agency details and list of children
					-- removing for now, until we decide we need it.
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
				*/ ?>
				<div class="fg fs oy-auto tab-content">
					<div role="tabpanel" class="pa4 tab-pane active clearfix" id="details">
						<div class="col-xs-6 pa0 pr4">
							<div class="pb">
								<label for="name" class="b">Agency name:
									<? if ($editing): ?>
										<span class="required">*</span>
									<? endif; ?>
								</label>
								<? if (!$editing): ?>
									<span><?= e($Agency->name); ?></span>
								<? else: ?>
									<input type="text"
										name="agency[name]"
										id="agency_name"
										value="<?=old('agency.name', $Agency->name ?? null);?>"
										class="form-control"
										required="required"
									>
									<?if ($errors->has('agency.name')): ?>
										<p class="validation-error">
											<?=e($errors->first('agency.name'));?>
										</p>
									<?endif;?>
								<? endif; ?>
							</div>

							<div class="pb">
								<label for="agency_id_prefix" class="b">
									ID Prefix:
									<? if ($editing): ?>
										<span class="required">*</span>
									<? endif; ?>
								</label>
									<input type="tel"
										name="agency[id_prefix]"
										id="agency_id_prefix"
										value="<?=old('agency.id_prefix', $Agency->id_prefix ?? null);?>"
										class="form-control"
									>
									<?if ($errors->has('agency.id_prefix')): ?>
										<p class="validation-error">
											<?=e($errors->first('agency.id_prefix'));?>
										</p>
									<?endif;?>
							</div>

							<div class="pb">
								<label for="address" class="b pb2 db">
									Address:
									<span class="required">*</span>
								</label>

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
									<input type="text" name="agency[address]" id="address"
										class="form-control mb" value="<?= e(old('agency.address', $Agency->address)); ?>">

									<?if ($errors->has('agency.address')): ?>
										<p class="validation-error">
											<?=e($errors->first('agency.address'));?>
										</p>
									<?endif;?>

									<input type="text" name="agency[address_2]" id="address_2"
										class="form-control" value="<?= e(old('agency.address_2', $Agency->address_2)); ?>">

									<?if ($errors->has('agency.address_2')): ?>
										<p class="validation-error">
											<?=e($errors->first('agency.address_2'));?>
										</p>
									<?endif;?>

									<div class="flex justify-start">
										<div class="pa pl0 fs-no w-33">
											<label for="city">City:
												<span class="required">*</span>
											</label>
											<input type="text" name="agency[city]" id="city"
												class="form-control" value="<?= e(old('agency.city', $Agency->city)); ?>">

											<?if ($errors->has('agency.city')): ?>
												<p class="validation-error">
													<?=e($errors->first('agency.city'));?>
												</p>
											<?endif;?>
										</div>

										<div class="pa pl0 fs-no w-33">
											<label for="state">State:
												<span class="required">*</span>
											</label>
											<select
												name="agency[state]"
												id="agency_state"
												class="form-control"
												>
												<?foreach (get_states() as $abbr => $name): ?>
													<option
														value="<?=e($abbr);?>"
														<?if ($abbr == old('agency.state', $Agency->state ?? 'PA')): ?>
															selected
														<?endif;?>
													>
														<?=e($abbr);?>
													</option>
												<?endforeach;?>
											</select>
											<?if ($errors->has('agency.state')): ?>
												<p class="validation-error">
													<?=e($errors->first('agency.state'));?>
												</p>
											<?endif;?>
										</div>

										<div class="pa ph0 fs-no w-33">
											<label for="zip">Zip:
												<span class="required">*</span>
											</label>
											<input type="tel" name="agency[zip]" id="zip"
												class="form-control" value="<?= e(old('agency.zip', $Agency->zip)); ?>">
											<?if ($errors->has('agency.zip')): ?>
												<p class="validation-error">
													<?=e($errors->first('agency.zip'));?>
												</p>
											<?endif;?>
										</div>
									</div>
								<? endif;?>
							</div>

							<div class="pb">
								<p class="b pb2">Contact</p>
								<? $Contact = $Agency->Contact()->first(); ?>

								<? if (!$editing): ?>
									<div class="ml3 pl3 py0 bl b--black-10 bw2">
										<? if ( ! $Contact): ?>
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
									</div>
								<? else: ?>
									<div class="pb">
										<label for="contact_name">Name:
											<span class="required">*</span>
										</label>
										<input type="text" name="contact[name]" id="contact_name"
											class="form-control" value="<?= e(old('contact.name', $Contact->name ?? null)); ?>">

										<?if ($errors->has('contact.name')): ?>
											<p class="validation-error">
												<?=e($errors->first('contact.name'));?>
											</p>
										<?endif;?>
									</div>

									<div class="pb">
										<label for="contact_email">Email:
											<span class="required">*</span>
										</label>
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-envelope"></i>
											</span>
											<input type="email"
												name="contact[email]"
												id="contact_email"
												value="<?=old('contact.email', $Contact->email ?? null);?>"
												class="form-control"
											>
										</div>
										<?if ($errors->has('contact.email')): ?>
											<p class="validation-error">
												<?=e($errors->first('contact.email'));?>
											</p>
										<?endif;?>
									</div>

									<div class="pb">
										<label for="contact_phone">Phone number:
											<span class="required">*</span>
										</label>
										<div class="input-group">
											<span class="input-group-addon">
												<i class="fa fa-phone"></i>
											</span>
											<input type="tel"
												name="contact[phone]"
												id="contact_phone"
												value="<?=old('contact.phone', $Contact->phone ?? null);?>"
												class="form-control"
											>
											<span class="input-group-addon">
												ext:
											</span>
											<input type="tel"
												name="contact[phone_extension]"
												id="contact_phone"
												value="<?=old('contact.phone_extension', $Contact->phone_extension ?? null);?>"
												class="form-control"
											>
										</div>

										<?if ($errors->has('contact.phone')): ?>
											<p class="validation-error">
												<?=e($errors->first('contact.phone'));?>
											</p>
										<?endif;?>

										<?if ($errors->has('contact.phone_extension')): ?>
											<p class="validation-error">
												<?=e($errors->first('contact.phone_extension'));?>
											</p>
										<?endif;?>
									</div>
								<? endif; ?>
							</div>
						</div>

						<div class="col-xs-6 pa0">
							<div class="pb">
								<? if (!$editing): ?>
									<p class="pb b">Login Credentials:</p>

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
								<? else: ?>
									<div class="pb">
										<label for="user_username">Login Username:
											<span class="required">*</span>
										</label>
										<input type="text"
											name="user[username]"
											id="user_username"
											value="<?=old('user.username', $Agency->User->username ?? null);?>"
											class="form-control"
										>
										<?if ($errors->has('user.username')): ?>
											<p class="validation-error">
												<?=e($errors->first('user.username'));?>
											</p>
										<?endif;?>
									</div>

									<label for="user_password">Password:</label>
									<? if ($Agency && $Agency->exists): ?>
										<a href="#" id="toggle-change-pw">Change Password</a>
									<? else: ?>
										<span class="required">*</span>
									<? endif; ?>

									<div id="password-fields" class="<?= $Agency && $Agency->exists && ! $errors->has('user.password') && ! $errors->has('user.confirm_password')? 'dn' : ''; ?>">
										<div class="pb">
											<input type="password"
												name="user[password]"
												id="user_password"
												value="<?=old('user.password', null);?>"
												class="form-control"
											>
											<?if ($errors->has('user.password')): ?>
												<p class="validation-error">
													<?=e($errors->first('user.password'));?>
												</p>
											<?endif;?>
										</div>

										<div class="pb">
											<label for="user_confirm_password">Confirm Login Password:
												<? if ( ! $Agency || !$Agency->exists): ?>
													<span class="required">*</span>
												<? endif; ?>
											</label>
											<input type="password"
												name="user[confirm_password]"
												id="user_confirm_password"
												value="<?=old('user.confirm_password', null);?>"
												class="form-control"
											>
											<?if ($errors->has('user.confirm_password')): ?>
												<p class="validation-error">
													<?=e($errors->first('user.confirm_password'));?>
												</p>
											<?endif;?>
										</div>
									</div>
								<? endif; ?>
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

			<div class="col-xs-4 pa0 flex flex-column justify-between">
				<div class="o-hidden oy-auto">
					<? if ($Agency->exists): ?>
						<div class="ma mv4 bg-white br3 py4 px4">
							<h1 class="ma0 pa0 f2 mb3">
								<i class="fa fa-comments-o"></i>
								Agency Notes
							</h1>

							<p class="f4 muted">
								All agency notes are <span class="i">internal only</span>. They are never
								shared with the agency partners.
							</p>

							<p class="bb b--black-20 mv2 mb4"></p>

							<agency-notes
								:initial-notes='<?= e($Agency->Note->toJson()); ?>'
								:agency='<?= e($Agency->toJson()); ?>'
							></agency-notes>
						</div>
					<? endif; ?>
				</div>

				<div class="bg-washed-blue pa bt b--black-20 fs-no fg-no">
					<button type="submit" class="btn btn-lg btn-block btn-success">
						<i class="fa fa-download"></i>
						Save
					</button>

					<a href="<?= route('admin.agency.index'); ?>" title="Cancel"
						class="btn btn-lg btn-default btn-ghost btn-block">
						<i class="fa fa-times-circle"></i>
						Cancel
					</a>

				</div>
			</div>
		<?= Form::close(); ?>
	</div>
@stop

@section('js')
<script type="text/javascript">
(function($) {
	$(function() {
		$('#toggle-change-pw').on('click', function() {
			$('#password-fields').toggle();
		});
	});
})(jQuery);
</script>
@stop

