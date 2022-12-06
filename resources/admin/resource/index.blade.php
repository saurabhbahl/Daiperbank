<? use App\Resource; ?>

@extends('layouts.app')

@section('content')

    <div class="breadcrumbs">
        <p class="crumb">Resources</p>
    </div>

    <div class="flex-auto flex justify-start content-stretch o-hidden">
        <div class="col-xs-8 bg-white br b--black-20 flex-auto oy-auto pa3">
            <table class="resourcesTable">
                <tr>
                    <th>File</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
                @foreach ($resources as $resource)
                    <tr>
                        <td>{{ $resource->file }}</td>
                        <td>{{ $resource->created_at }}</td>
                        <td>{{ $resource->updated_at }}</td>
                        <td>
                            <form action="{{ route('admin.resource.destroy', $resource->id) }}" method="GET">
                                <a class="btn btn-primary" href="{{ route('admin.resource.edit', $resource->id) }}">Edit</a>
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </table>
        </div>

        <div class="col-xs-4 pb oy-auto">
            <div class="ma mv4 bg-white br3 py4 px4">
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
                <form method="post" class="pxa pf flex justify-start content-stretch o-hidden"
                    enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="col-xs-8 pa0 bg-white br b--black-20 flex-auto flex flex-column justify-stretch o-hidden">
                        <div class="fg fs oy-auto tab-content">
                            <div role="tabpanel" class="pa4 tab-pane active clearfix" id="details">
                                <div class="col-xs-12 pa0 pr4">
                                    <div class="pb">
                                        <label for="file" class="b">File:
                                            <span class="required">*</span>
                                        </label>
                                        <input type="file" name="file" id="file" class="form-control"
                                            required="required"
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
    </div>
@stop
