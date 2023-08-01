@extends('layouts.report')

@section('orientation')
landscape
@stop

@section('report-title')
Diaper Usage Report
@stop

<? $uniqueValues = array(); ?>
@section('content')
        <div class="flex justify-between mb3">
				<div class="w-100 fs-no fg-no pr">
					<h3 class="f3 mt0 pa0"><?= $Agency->name ?></h3>
						<? $i = 0; foreach($stats as $data): ?> 
							<? if (!in_array($data->id, $uniqueValues)) { 
								if($i > 0){
									echo "</tbody></table>";
								}
								?>
								<table class="table table-bordered table-striped">
								<thead>
									<tr>
										<th style="width:25%">Name</th>
										<th style="width:25%" colspan="3"><? echo $data->name; ?></th>
									</tr>
									<tr>
										<th style="width:25%">Order Number</th>
										<th style="width:25%">Size</th>
										<th style="width:25%">Qty</th>
										<th style="width:25%">Pick-up date</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td style="width:25%"><?= number_format($data->ordernumber,0); ?></td>									
										<td style="width:25%"><?= $data->productname ?></td>									
										<td style="width:25%"><?= $data->qty ?></td>
										<td style="width:25%"><?= $data->pickupdate ?></td>
									</tr>
							<?php
								$uniqueValues[] = $data->id;
							}	
							else{ ?>
								
									<tr>
										<td style="width:25%"><?= number_format($data->ordernumber,0); ?></td>
										<td style="width:25%"><?= $data->productname ?></td>
										<td style="width:25%"><?= $data->qty ?></td>
										<td style="width:25%"><?= $data->pickupdate ?></td>
									</tr>
							<?	} ?>
							
							<? endforeach; ?>
				</div>
		</div>
	
@stop