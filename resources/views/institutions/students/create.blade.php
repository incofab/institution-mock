<?php
$title = 'Add Student - Institution'; ?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Students
		</h1>
		<p>Register a student in this institution</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{instRoute('students.index')}}">Students</a></li>
		<li class="breadcrumb-item">Create/Update</li>
	</ul>
</div>
@include('common.message')
<div>
	<div class="tile">
		<h3 class="tile-title">Create/Update Student</h3>
		<form action="{{$edit ? instRoute('students.update', $edit) : instRoute('students.store')}}" method="post">
    		@csrf
            @if ($edit)
                @method('PUT')
            @endif
    		<div class="tile-body">
				<div class="form-group">
					<label class="control-label">Firstname</label> 
					<input type="text" id="" name="firstname" value="{{old('firstname', $edit?->firstname)}}" 
						placeholder="Firstname" class="form-control" >
				</div>
				<div class="form-group">
					<label class="control-label">Lastname</label> 
					<input type="text" id="" name="lastname" value="{{old('lastname', $edit?->lastname)}}" 
						placeholder="Lastname" class="form-control" >
				</div>
				<div class="form-group">
					<label class="control-label">Class</label> 
					<select name="grade_id" id="select-grade" class="form-control">
    					<option value="">Select Class</option>
    					@foreach($allGrades as $grade)
    						<option value="{{$grade->id}}" @selected($grade->id == old('grade_id', $edit?->grade_id))
    						title="{{$grade->description}}" >{{$grade->title}}</option>
    					@endforeach
					</select>
				</div>
				<div class="form-group">
					<label class="control-label">Email [Optional]</label> 
					<input type="email" id="" name="email" value="{{old('email', $edit?->email)}}" 
						placeholder="Email" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label">Phone [optional]</label> 
					<input type="text" id="" name="phone" value="{{old('phone', $edit?->phone)}}" 
					placeholder="Reachable Mobile number" class="form-control">
				</div>
				<div class="form-group">
					<label class="control-label">Address [optional]</label>
					<textarea class="form-control" rows="3" name="address"
					placeholder="Address of the exam center">{{old('address', $edit?->address)}}</textarea>
				</div>
    		</div>
    		<div class="tile-footer">
				{{-- <input type="hidden" name="reference" value="{{uniqid()}}" /> --}}
    			<button class="btn btn-primary" type="submit">
    				<i class="fa fa-fw fa-lg fa-check-circle"></i> Submit
    			</button>
    		</div>
		</form>
	</div>

</div>

@endsection