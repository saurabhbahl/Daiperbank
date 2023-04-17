@extends('layouts.report')

@section('orientation')
landscape
@stop

@section('report-title')
Diaper Usage Report
@stop

@section('content')
		<div class="flex justify-between mb3">
				<div class="w-100 fs-no fg-no pr">
					<h3 class="f3 mt0 pa0">{{$Agency->name}}</h3>
						@foreach($stats as $data)
						<div class="col-lg-6">
							<table class="table table-bordered table-striped">
								<tr>
									<th scope="row" class="w-50 tr">Name</th>
									<td>{{$data->name}}</td>
								</tr>
								<tr>
									<th scope="row" class="tr">Order Number</th>
									<td>{{$data->ordernumber}}</td>
								</tr>
								<tr>
									<th scope="row" class="tr">Size</th>
									<td>{{$data->productname}}</td>
								</tr>
								<tr>
									<th scope="row" class="tr">Product Category</th>
									<td>{{$data->productcategory}}</td>
								</tr>
								<tr>
									<th scope="row" class="tr">Pickup Date</th>
									<td>{{$data->pickupdate}}</td>
								</tr>
							</table>
						</div>
						@endforeach
				</div>
		</div>
	
@stop