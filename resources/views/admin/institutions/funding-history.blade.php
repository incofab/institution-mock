<?php
$title = 'Admin - Funding History';
?>
@extends('admin.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Funding History
		</h1>
		<p>Global license funding history</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{route('admin.dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Funding History</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
	<div class="tile-body">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>Institution</th>
					<th>Amount</th>
					<th>License Cost</th>
					<th>Licenses</th>
					<th>Bonus Licenses</th>
					<th>Balance Amount</th>
					<th>License Balance</th>
					<th>Source</th>
					<th>Comment</th>
					<th>Funded By</th>
					<th>Date</th>
				</tr>
			</thead>
			<tbody>
				@forelse($allRecords as $record)
					<tr>
						<td>{{$record->institution?->name}}</td>
						<td>{{number_format($record->amount, 2)}}</td>
						<td>{{number_format($record->license_cost, 2)}}</td>
						<td>{{$record->num_of_licenses}}</td>
						<td>{{$record->bonus_licenses}}</td>
						<td>{{number_format($record->balance_amount, 2)}}</td>
						<td>{{$record->license_balance_before}} / {{$record->license_balance_after}}</td>
						<td>{{ucfirst($record->source)}}</td>
						<td>{{$record->comment}}</td>
						<td>{{$record->user?->name}}</td>
						<td>{{$record->created_at}}</td>
					</tr>
				@empty
					<tr>
						<td colspan="11" class="text-center">No funding history</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<div class="tile-footer">
		@include('common.paginate', ['paginatedData' => $allRecords])
	</div>
</div>

@endsection
