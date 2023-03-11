<? use App\Resource; ?>

@extends('layouts.app')

@section('content')

    <div class="breadcrumbs">
        <p class="crumb">Resources</p>
    </div>

    <div class="flex-auto flex justify-start content-stretch o-hidden">
        <div class="col-xs-8 bg-white br b--black-20 flex-auto oy-auto pa3">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('admin.resource.update', $resource->id) }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                {{ method_field('PUT') }}
                <div class="col-xs-12 pa0 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden">
                    <div class="fg fs oy-auto tab-content">
                        <div role="tabpanel" class="pa4 tab-pane active clearfix" id="details">
                            <div class="col-xs-12 pa0 pr4">
                                <div class="pb">
                                    <label for="file" class="b">File:
                                        <span class="required">*</span>
                                    </label>
                                    <input type="file" name="file" id="file" class="form-control"
                                        required="required" value="{{ $resource->file }}"
                                        placeholder="{{ $resource->file }}"
                                        accept=".pdf,.csv,.xml,.doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                                    <strong>Allowed Only PDF, CSV, XML, DOCX</strong>
                                </div>
                                <button type="submit" class="btn btn-lg btn-block btn-success"><i
                                        class="fa fa-download"></i>
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <p>{{ $message }}</p>
                </div>
            @endif
        </div>
    </div>
@stop
