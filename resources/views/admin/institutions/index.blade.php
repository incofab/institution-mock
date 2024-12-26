<?php
$title = 'Admin - All Institutions'; ?>
@extends('admin.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Institution
		</h1>
		<p>List of all Institutions</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{route('admin.dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Institutions</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
    <div class="tile-header clearfix mb-3">
    	<a href="{{route('admin.institutions.create')}}" class="btn btn-primary pull-right">
    		<i class="fa fa-plus"></i> New
    	</a>
    </div>
    <div class="tile-body">
    	<table class="table table-hover table-bordered" id="data-table" >
    		<thead>
    			<tr>
    				<th>Code</th>
    				<th>Name</th>
    				<th>Email</th>
    				<th>Phone</th>
    				<th><i class="fa fa-bars"></i></th>
    			</tr>
    		</thead>
			@foreach($allRecords as $record)
				<tr title="Address: {{$record->address}}" >
					<td>{{$record['code']}}</td>
					<td>{{$record['name']}}</td>
					<td>{{$record['email']}}</td>
					<td>{{$record['phone']}}</td>
					<td>
						<a href='{{route('admin.institutions.assign-user', $record)}}' class='btn btn-link'>
							<i class='fa fa-user'></i> Assign User
						</a>
						<a href='{{route('institutions.dashboard', $record->code)}}' class='btn btn-link'>
							<i class='fa fa-hand-point-right'></i> Goto Page
						</a>
						<a href='{{route('admin.institutions.edit', $record)}}' class='btn btn-link'>
							<i class='fa fa-edit'></i> Edit
						</a>
						@include('common._delete_form', ['deleteRoute' => route('admin.institutions.destroy', $record)])
					</td>
				</tr>
			@endforeach
		</table>
	</div>
	<div class="tile-footer">
		@include('common.paginate')
	</div>
</div>

<!-- Data table plugin-->
<script type="text/javascript">
$(function () {
//   $('[data-toggle="popover"]').popover();
  var popOverSettings = {
		    selector: '[data-toggle="popover"]', //Sepcify the selector here
//	 	    content: function () {
//	 	        return $('#popover-content').html();
//	 	    }
	}
	
	$('#data-table').popover(popOverSettings);
})
</script>

@stop
