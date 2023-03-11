@extends('layouts.app')

@section('content')
    <div class="breadcrumbs">
        <p class="crumb">
            Agreement
        </p>
    </div>

    <div class="flex-auto flex justify-start content-stretch o-hidden pxr">
        <div class="col-xs-12 bg-white br b--black-20 flex-auto o-hidden flex flex-column justify-stretch partnerHandbook">
            @if ($agreements->count() == 0)
            <div class="agreementAlert bg-washed-red pa4">
                <strong>Whoops!</strong> no agreement have yet.
            </div>
            @else
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
                    @foreach ($agreements as $agreement)
                    <tr>
                        <td>{{ $agreement->file }}</td>
                        <td>{{ $agreement->created_at }}</td>
                        <td>{{ $agreement->updated_at }}</td>
                        <td>
                            <a href="/uploads/agreements/{{ $agreement->file }}" class="btn btn-primary btn-block" download>
                                Download
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

@stop
