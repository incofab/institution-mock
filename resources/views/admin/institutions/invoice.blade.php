<?php
$title = 'Admin - Generate Institution Invoice';
$oldCharges = old('extra_charges', [['label' => '', 'amount' => '']]);
?>
@extends('admin.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Institution Invoice
		</h1>
		<p>Generate invoice for {{$institution->name}}</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{route('admin.dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{route('admin.institutions.index')}}">Institution</a></li>
		<li class="breadcrumb-item">Generate Invoice</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
	<h3 class="tile-title">Generate {{$institution->name}} Invoice</h3>
	<div class="tile-body">
		<div class="mb-3">
			<p><b>Current Licenses: </b> {{$institution->licenses}}</p>
			<p><b>License Cost: </b> {{number_format($institution->license_cost, 2)}}</p>
		</div>
		<form action="{{route('admin.institutions.invoice.store', $institution)}}" method="post">
			@csrf
			<h5>Extra Charges</h5>
			<p class="text-muted">Add optional invoice line items such as setup, support, or service charges.</p>
			<div id="extra-charges">
				@foreach($oldCharges as $index => $charge)
					<div class="form-row align-items-end mb-2 extra-charge-row">
						<div class="form-group col-md-7">
							<label class="control-label">Charge</label>
							<input type="text" name="extra_charges[{{$index}}][label]"
								value="{{$charge['label'] ?? ''}}" placeholder="Charge name" class="form-control">
						</div>
						<div class="form-group col-md-4">
							<label class="control-label">Amount</label>
							<input type="number" name="extra_charges[{{$index}}][amount]"
								value="{{$charge['amount'] ?? ''}}" min="0.01" step="0.01" placeholder="0.00" class="form-control">
						</div>
						<div class="form-group col-md-1">
							<button class="btn btn-danger btn-block remove-charge" type="button">
								<i class="fa fa-times"></i>
							</button>
						</div>
					</div>
				@endforeach
			</div>
			<button class="btn btn-secondary mb-3" id="add-charge" type="button">
				<i class="fa fa-plus"></i> Add Charge
			</button>
			<div>
				<button class="btn btn-primary" type="submit">
					<i class="fa fa-fw fa-lg fa-file-invoice"></i> Generate Invoice
				</button>
			</div>
		</form>
	</div>
</div>

<script type="text/javascript">
$(function () {
	var chargeIndex = {{count($oldCharges)}};

	function chargeRow(index) {
		return '<div class="form-row align-items-end mb-2 extra-charge-row">' +
			'<div class="form-group col-md-7">' +
			'<label class="control-label">Charge</label>' +
			'<input type="text" name="extra_charges[' + index + '][label]" placeholder="Charge name" class="form-control">' +
			'</div>' +
			'<div class="form-group col-md-4">' +
			'<label class="control-label">Amount</label>' +
			'<input type="number" name="extra_charges[' + index + '][amount]" min="0.01" step="0.01" placeholder="0.00" class="form-control">' +
			'</div>' +
			'<div class="form-group col-md-1">' +
			'<button class="btn btn-danger btn-block remove-charge" type="button"><i class="fa fa-times"></i></button>' +
			'</div>' +
			'</div>';
	}

	$('#add-charge').on('click', function () {
		$('#extra-charges').append(chargeRow(chargeIndex));
		chargeIndex++;
	});

	$('#extra-charges').on('click', '.remove-charge', function () {
		$(this).closest('.extra-charge-row').remove();
	});
});
</script>

@endsection
