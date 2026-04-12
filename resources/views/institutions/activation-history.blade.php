<?php
$title = 'Institution - Activation History';
?>
@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Activation History
		</h1>
		<p>Exam activation history</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Activation History</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
	<div class="tile-body">
		<table class="table table-hover table-bordered">
			<thead>
				<tr>
					<th>Event</th>
					<th>Exams</th>
					<th>Licenses</th>
					<th>License Balance</th>
					<th>Activated By</th>
					<th>Activated At</th>
				</tr>
			</thead>
			<tbody>
				@forelse($allRecords as $record)
					<tr>
						<td>{{$record->event?->title}}</td>
						<td>{{$record->num_of_exams}}</td>
						<td>{{$record->licenses}}</td>
						<td>{{$record->license_balance_before}} / {{$record->license_balance_after}}</td>
						<td>{{$record->activatedByUser?->name}}</td>
						<td>{{$record->activated_at}}</td>
					</tr>
				@empty
					<tr>
						<td colspan="6" class="text-center">No activation history</td>
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
