@extends('layouts.app')

@section('content')
    <div class="breadcrumbs">
        <p class="crumb">
            Additional Resouces
        </p>
    </div>

    <div class="flex-auto flex justify-start content-stretch o-hidden pxr">
        <div class="col-xs-12 bg-white br b--black-20 flex-auto o-hidden flex flex-column justify-stretch partnerHandbook">
            <table>
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Created at</th>
                        <th>Updated at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($resources as $resource)
                        <tr>
                            <td>{{ $resource->file }}</td>
                            <td>{{ $resource->created_at }}</td>
                            <td>{{ $resource->updated_at }}</td>
                            <td>
                                <a href="/uploads/{{ $resource->file }}" class="btn btn-primary btn-block" download>
                                    Download
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop
