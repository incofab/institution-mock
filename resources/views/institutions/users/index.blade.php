<?php
$title = 'Institution - Users'; ?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Institution Users
		</h1>
		<p>Manage users who can access this institution</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Users</li>
	</ul>
</div>
@include('common.message')
<div class="row">
	<div class="col-md-4">
		<div class="tile">
			<h3 class="tile-title">Add Institution User</h3>
			<form action="{{instRoute('users.store')}}" method="post">
				@csrf
				<div class="tile-body">
					<div class="form-group">
						<label class="control-label">Email</label>
						<input type="text" name="email" value="{{old('email')}}"
							placeholder="Existing user email" class="form-control">
					</div>
					<div class="form-group">
						<label class="control-label">Role</label>
						<select name="role" id="" class="form-control" required>
							<option value="">Select role</option>
							@foreach($roles as $role)
								<option value="{{$role->value}}" @selected(old('role') === $role->value)>
									{{$role->label()}}
								</option>
							@endforeach
						</select>
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
	<div class="col-md-8">
		<div class="tile">
			<div class="tile-body">
				<table class="table table-hover table-bordered" id="data-table">
					<thead>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th>Role</th>
							<th>Status</th>
						</tr>
					</thead>
					@foreach($allRecords as $record)
						<tr>
							<td>{{$record->user?->name}}</td>
							<td>{{$record->user?->email}}</td>
							<td>{{$record->role?->label()}}</td>
							<td>{{$record->status}}</td>
						</tr>
					@endforeach
				</table>
			</div>
			<div class="tile-footer">
				@include('common.paginate')
			</div>
		</div>
	</div>
</div>

@stop
