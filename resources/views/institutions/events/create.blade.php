<?php
$title = 'Register Event'; ?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Events
		</h1>
		<p>Create Event</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{instRoute('events.index')}}">Events</a></li>
		<li class="breadcrumb-item">Create Event</li>
	</ul>
</div>
@include('common.message')
<div>
	<div class="tile">
		<h3 class="tile-title">Create Event</h3>
		<form action="{{instRoute('events.store')}}" method="post">
			@csrf
            @if ($edit)
                @method('PUT')
            @endif
    		<div class="tile-body">
				<div class="form-group w-75" >
					<label class="control-label">Title</label>
					<input type="text" name="title" value="{{old('title', $edit?->title)}}" class="form-control"
						placeholder="Enter title" />
				</div>
				<div class="form-group w-75" >
					<label class="control-label">Description</label>
					<textarea name="description" class="form-control" 
						rows="3" placeholder="Enter description" >{{old('description', $edit?->description)}}</textarea>
				</div>
				<div class="form-group w-75" >
					<label class="control-label">Duration in mins</label>
					<input type="text" name="duration" value="{{old('duration', $edit?->duration)}}" class="form-control"
						placeholder="Enter duration in mins" />
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