@extends('layouts.app')

@section('content')
	<div class="breadcrumbs">
		<p class="crumb">Send Mail</p>
	</div>
	<div class="flex-auto flex justify-start content-stretch o-hidden">
		<div class="col-xs-8 bg-white br b--black-20 pa4 flex-auto flex flex-column justify-stretch o-hidden pa0">
			<div class="fg fs oy-auto pa4">
				<form action="<? echo route('admin.agencymail'); ?>" method="post" >
				<label>Message</label></br>
				<textarea rows="6" cols="60" name="message"></textarea>
				{{ csrf_field() }}
					<? foreach ($Agencies as $Agency): ?>
						<? $Contact = $Agency->Contact->first(); ?>

						<? if ($Contact): ?>
							<? if ($Contact->email): ?>
								<p class="agencyemail__input">
									<label for="agency_email" id="agency_send_email">
										<input name="a_mail[]" type="checkbox" value="<?= e($Contact->email) ?>"><?= e($Contact->name) ?></label>
								</p>
							<? endif; ?>
						<? endif; ?>
					<? endforeach ?>
					<button  type="submit" class="btn btn-lg btn-success">Send Mail</button>
				</form>
			</div>
		</div>
	</div>
@stop