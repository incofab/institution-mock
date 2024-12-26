<?php
$title = 'Institution - All Classes';
$confirmMsg = 'Are you sure?';
?>
@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Student Classes
		</h1>
		<p>List of all student classes in this Institution</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Student Classes</li>
	</ul>
</div>
@include('common.message')
<div class="tile" id="all-students">
    <div class="tile-header clearfix mb-3">
    	<a href="{{instRoute('grades.create')}}" class="btn btn-primary float-right"><i class="fa fa-plus"></i> New</a>
    </div>
    <div class="tile-body">
    	<table class="table table-hover table-bordered" id="data-table" >
    		<thead>
    			<tr>
    				<th>S/No</th>
    				<th>Title</th>
    				<th>Description</th>
    				{{-- <th>Exam</th> --}}
    				<th><i class="fa fa-bars p-2"></i></th>
    			</tr>
    		</thead>
    		<?php $i = 0; ?>
			@foreach($allRecords as $record)
    		<?php $i++; ?>
				<tr>
					<td>{{$i}}</td>
					<td>{{$record['title']}}</td>
					<td>{{$record['description']}}</td>
					{{-- <td>
						<a href="{{instRoute('exams.events.grades.create')}}" 
							class="btn btn-info btn-sm"><i class="fa fa-graduation-cap"></i> Register Exam</a>
					</td> --}}
					<td>
						<a href="{{instRoute('grades.edit', [$record->id])}}" class="btn btn-primary btn-sm">
							<i class="fa fa-edit"></i> Edit</a>
						<a href="{{instRoute('students.index', ['grade' => $record->id])}}" class="btn btn-warning btn-sm">
							<i class="fa fa-users"></i> View Student</a>
						@include('common._delete_form', [
							'deleteRoute' =>instRoute('grades.destroy', $record),
							'btnClasses' => 'btn btn-danger btn-sm'
						])
					</td>
				</tr>
			@endforeach
		</table>
	</div>
	<div class="tile-footer">
		@include('common.paginate')
	</div>
</div>

@stop
