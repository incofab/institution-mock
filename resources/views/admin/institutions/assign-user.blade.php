<?php
$title = 'Admin - Assign User to Institution'; ?>

@extends('admin.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Institution
		</h1>
		<p>Add User to Institution</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{route('admin.dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{route('admin.institutions.index')}}">Institution</a></li>
		<li class="breadcrumb-item">Institution User</li>
	</ul>
</div>
@include('common.message')
<div>
	<div class="tile">
		<h3 class="tile-title">Add Institution User</h3>
		<form action="{{route('admin.institutions.assign-user', $institution->code)}}" method="post">
			@csrf
    		<div class="tile-body">
    			<div>
    				Add a user to this institution <strong>{{$institution->name}}</strong>
    			</div>
    			<br />
				<div class="form-group">
					<label class="control-label">Email</label> 
					<input type="text" id="" name="email" value="{{old('email')}}" 
						placeholder="Email" class="form-control" >
				</div>
    		</div>
    		<div class="tile-footer">
    			<button class="btn btn-primary" type="submit">
    				<i class="fa fa-fw fa-lg fa-check-circle"></i>Add Now
    			</button>
    		</div>
		</form>
	</div>

</div>

@endsection