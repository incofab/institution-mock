<?php
$title = 'Add Student Class'; ?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Student Classes
		</h1>
		<p>Register a Class for students</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{instRoute('grades.index')}}">Classes</a></li>
		<li class="breadcrumb-item">Register/Update</li>
	</ul>
</div>
@include('common.message')
<div>
	<div class="tile">
		<h3 class="tile-title">Create/Update Class</h3>
		<form action="{{$edit ? instRoute('grades.update', $edit) : instRoute('grades.store')}}" method="post">
    		@csrf
            @if ($edit)
                @method('PUT')
            @endif
    		<div class="tile-body">
				<div class="form-group">
					<label class="control-label">Title</label> 
					<input type="text" id="" name="title" value="{{old('title', $edit?->title)}}" 
						placeholder="Class Name" class="form-control" >
				</div>
				<div class="form-group">
					<label class="control-label">Description</label>
					<textarea name="description" id="" rows="3" class="form-control"
					>{{old('description', $edit?->description)}}</textarea> 
				</div>
    		</div>
    		<div class="tile-footer">
    			<button class="btn btn-primary" type="submit">
    				<i class="fa fa-fw fa-lg fa-check-circle"></i> Submit
    			</button>
    		</div>
		</form>
	</div>

</div>

@endsection