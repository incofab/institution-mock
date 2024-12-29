<?php
$title = 'Admin - Create/Update Institution';
$edit = isset($edit) ? $edit : null;
?>

@extends('admin.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Institution
		</h1>
		<p>Register/Update an Institution</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{route('admin.dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{route('admin.institutions.index')}}">Institution</a></li>
		<li class="breadcrumb-item">Register/Update</li>
	</ul>
</div>
<div>
	<div class="tile">
		<h3 class="tile-title">Register/Update Institution</h3>
		<form action="{{route('admin.institutions.store')}}" method="post">
			@include('common.message')
			@csrf
            @if ($edit)
                @method('PUT')
            @endif
    		<div class="tile-body">
				<div class="form-group">
					<label class="control-label">Institution Name</label> 
					<input type="text" id="" name="name" value="{{old('name', $edit?->name)}}" 
						placeholder="Name of the Institution" class="form-control" >
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
						placeholder="Address of the Institution">{{old('address', $edit?->address)}}</textarea>
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