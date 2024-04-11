@extends('layouts.app')

@section('content')
    <div class="breadcrumbs">
        <p class="crumb">Send Mail</p>
    </div>
    <div class="flex-auto flex justify-start content-stretch o-hidden">
        <div class="col-xs-8 bg-white br b--black-20 pa4 flex-auto flex flex-column justify-stretch o-hidden pa0">
            <div class="fg fs oy-auto pa4">
			@if(session('success'))
				<div class="alert alert-success">
					{{ session('success') }}
				</div>
			@endif
                <form action="{{ route('admin.agencymail') }}" method="post">
                    <label>Subject <span class="text-danger">*</span></label><br>
                    <input type="text" name="subject" class="mb4 form-control" value="{{ old('subject') }}"><br>
                    @if($errors->has('subject'))
                        <span class="text-danger">{{ $errors->first('subject') }}</span>
                    @endif
                    <br>
                    <label>Message</label><br>
                    <!-- <textarea rows="6" cols="60" name="message" class="mb4 form-control"></textarea> -->
                    <agency-editor></agency-editor>
                    <label>Select Atleast one Agency</label>
                    @if($errors->has('a_mail'))
                        <span class="text-danger">({{ $errors->first('a_mail') }})</span>
                    @endif
                    {{ csrf_field() }}
                    @foreach ($Agencies as $Agency)
                        @php
                            $Contact = $Agency->Contact->first();
                        @endphp
                        @if ($Contact && $Contact->email)
                            <p class="agencyemail__input">
                                <label for="agency_email" id="agency_send_email">
                                    <input name="a_mail[]" type="checkbox" value="{{ e($Contact->email) }}" @if(is_array(old('a_mail')) && in_array($Contact->email, old('a_mail'))) checked @endif>{{ e($Contact->name) }}
                                </label>
                            </p>
                        @endif
                    @endforeach
                    <button type="submit" class="btn btn-lg btn-success mt3">Send Mail</button>
                </form>
            </div>
        </div>
    </div>
@stop
