<?php
$title = 'Institution - Fund Licenses';
?>
@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Fund Licenses
		</h1>
		<p>Fund institution licenses</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
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
		<form action="{{instRoute('fund-licenses.store')}}" method="post">
			@csrf
			<div class="form-group">
				<label class="control-label">Amount</label>
				<input type="number" id="" name="amount" value="{{old('amount')}}"
					min="0.01" step="0.01" placeholder="Funding amount" class="form-control" required>
			</div>
			<div class="form-group">
				<label class="control-label">Payment Gateway</label>
				<select name="gateway" class="form-control" required>
					@foreach($gateways as $key => $label)
						<option value="{{$key}}" @selected(old('gateway') === $key)>{{$label}}</option>
					@endforeach
				</select>
			</div>
			<button class="btn btn-primary" type="submit">
				<i class="fa fa-fw fa-lg fa-check-circle"></i> Continue
			</button>
		</form>
	</div>
</div>

@endsection
