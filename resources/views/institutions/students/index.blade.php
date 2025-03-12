<?php
$title = 'Institution - All Students';
$confirmMsg = 'Are you sure?';
?>
@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Students
		</h1>
		<p>List of all students in this Institution</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Students</li>
	</ul>
</div>
@include('common.message')
<div class="tile" id="all-students">
    <div class="tile-header clearfix mb-3">
    	<a href="{{instRoute('students.create')}}" class="btn btn-primary float-left"><i class="fa fa-plus"></i> New</a>
		<div class="form-group row float-right">
			<label for="select-grade" class="col-sm-5 col-form-label">Select Class</label>
			<div class="col-sm-7">
				<select name="grade_id" id="select-grade" class="form-control">
					<option value="">All Classes</option>
					@foreach($allGrades as $grade)
						<option value="{{$grade->id}}" @selected($grade->id == request('grade'))
						title="{{$grade->description}}" >{{$grade->title}}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
    <div class="tile-body">
    	<table class="table table-hover table-bordered" id="data-table" >
    		<thead>
    			<tr>
    				<th>Student ID</th>
    				<th>Name</th>
    				<th>Class</th>
    				<th>Phone</th>
    				<th>Email</th>
    				<th><i class="fa fa-bars p-2"></i></th>
    			</tr>
    		</thead>
			@foreach($allRecords as $record)
				<?php $grade = $record->grade; ?>
				<tr>
					<td>{{$record['code']}}</td>
					<td>{{$record['lastname']}} {{$record['firstname']}}</td>
					<td>{{$record->grade?->title}}</td>
					<td>{{$record['phone']}}</td>
					<td>{{$record['email']}}</td>
					<td>
						<a href="{{instRoute('exams.create', $record)}}" class='btn btn-link'>
							<i class='fa fa-graduation-cap'></i> Register Exam
						</a>
						<a href='{{instRoute('students.edit', [$record['id']])}}' class='btn btn-link'>
							<i class='fa fa-edit'></i> Edit
						</a>
						@include('common._delete_form', ['deleteRoute' => instRoute('students.destroy', $record)])
					</td>
				</tr>
			@endforeach
		</table>
	</div>
	<div class="tile-footer">
		@include('common.paginate')
	</div>
</div>
<script>
$(function () {
	$('#select-grade').on('change', function(e) {
		var url = "{{instRoute('students.index', ['grade' => '--'])}}";
		var selectedEventId = $(this).val();
		window.location.href = url.replace('--', selectedEventId);
	});
});
</script>
@endsection
