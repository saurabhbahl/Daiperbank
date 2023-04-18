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
					<h3 class="f3 mt0 pa0"><?= $Agency->name ?></h3>
						
				</div>
		</div>
	
@stop