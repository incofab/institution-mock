<?php
$title = 'Admin - Fund Institution Licenses';
?>
@extends('admin.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Institution Funding
		</h1>
		<p>Fund institution licenses</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{route('admin.dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{route('admin.institutions.index')}}">Institution</a></li>
		<li class="breadcrumb-item">Fund Licenses</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
	<h3 class="tile-title">Fund {{$institution->name}}</h3>
	<div class="tile-body">
		<div class="mb-3">
			<p><b>Current Licenses: </b> {{$institution->licenses}}</p>
			<p><b>License Cost: </b> {{number_format($institution->license_cost, 2)}}</p>
		</div>
		<form action="{{route('admin.institutions.fund.store', $institution)}}" method="post">
			@csrf
			<div class="form-group">
				<label class="control-label">Amount</label>
				<input type="number" id="" name="amount" value="{{old('amount')}}"
					min="0.01" step="0.01" placeholder="Funding amount" class="form-control" required>
			</div>
			<button class="btn btn-primary" type="submit">
				<i class="fa fa-fw fa-lg fa-check-circle"></i> Fund Licenses
			</button>
		</form>
	</div>
</div>

<div class="tile">
	<h3 class="tile-title">Recent Funding</h3>
	<div class="tile-body">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>Amount</th>
					<th>License Cost</th>
					<th>Licenses</th>
					<th>Balance Amount</th>
					<th>License Balance</th>
					<th>Source</th>
					<th>Funded By</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@forelse($fundings as $funding)
					<tr>
						<td>{{number_format($funding->amount, 2)}}</td>
						<td>{{number_format($funding->license_cost, 2)}}</td>
						<td>{{$funding->num_of_licenses}}</td>
						<td>{{number_format($funding->balance_amount, 2)}}</td>
						<td>{{$funding->license_balance_before}} / {{$funding->license_balance_after}}</td>
						<td>{{ucfirst($funding->source)}}</td>
						<td>{{$funding->user?->name}}</td>
						<td>{{$funding->created_at}}</td>
					</tr>
				@empty
					<tr>
						<td colspan="8" class="text-center">No funding record</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>

@endsection
