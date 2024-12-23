@extends('layouts.app')

@section('content')

    <div class="breadcrumbs">
        <p class="crumb">Partner Agreements</p>
    </div>

    <div class="flex-auto flex justify-start content-stretch o-hidden">
        <div class="col-xs-7 bg-white br b--black-20 flex-auto oy-auto pa3">
            @if ($agreements->count() == 0)
            <div class="alert alert-danger">
                <strong>Whoops!</strong> no agreement have yet.
            </div>
            @else
            <table class="tbl">
                <tr>
                    <th>File</th>
                    <th>Agency/Partner Name</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    <th>Actions</th>
                </tr>
                @foreach ($agreements as $agreement)
                    <tr>
                    <td><a href="{{ '/uploads/' . $resource->file }}" download>{{ $resource->file }}</a></td>
                        <td>{{ $agreement->name }}</td>
                        <td>{{ $agreement->created_at }}</td>
                        <td>{{ $agreement->updated_at }}</td>
                        <td>
                            <form action="{{ route('admin.agreement.destroy', $agreement->id) }}" method="GET">
                                <a class="btn btn-primary" href="{{ route('admin.agreement.edit', $agreement->id) }}">Edit</a>
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>

                    </tr>
                @endforeach
            </table>
            @endif
        </div>

        <div class="col-xs-5 pb oy-auto">
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
                <form method="post" class="pxa pf flex justify-start content-stretch o-hidden" enctype="multipart/form-data">
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
                                    <div class="pb">
                                        <label for="agency/partner" class="b">Agency/Partner: <span class="required">*</span>
                                        </label>
                                        <input type="text" name="agency_id" id="agency_name" class="form-control"
                                        required="required" placeholder="Search Agency/Partner Name..." />
                                        <div id="agency_list"></div>
                                    </div>
                                    <div class="py">
                                        <button type="submit" class="btn btn-lg btn-block btn-success"><i
                                            class="fa fa-download"></i>
                                        Save
                                    </button>
                                    </div>
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
@section('js')
<script>
    $(document).ready(function(){
        $('#agency_name').on('keyup',function(){
            var val = $(this).val();
            $.ajax({
                url:"{{route('admin.agreement.create')}}",
                type:"GET",
                 data:{'name':val},
                 success:function(data){
                    $("#agency_list").html(data);
                 }
            })
    });
    // $(document).on('click','li', function(){
    //     var value = $(this).text();
    //     // var attrValue = $(this).attr("value");
    //     // $('#agency_name').attr('placeholder',value);
    //     $('#agency_name').val(value);
    //     $('#agency_list').html('');
    // });
        $(document).on('click','select', function(){
            $('#agency_name').hide();
        });
    });
</script>
@endsection 