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
		<form action="{{$edit ? instRoute('events.update', $edit) : instRoute('events.store')}}" method="post">
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
				{{-- <div class="form-check">
					<input type="checkbox" value="1" id="flexCheckChecked" name="for_external"
					@checked(old('for_external', $event?->for_external))>
					<label for="flexCheckChecked">
						Use system supplied questions
					</label>
				</div> --}}
				@if (!$edit)
				<div class="form-group">
					<label class="control-label">External Content</label>
					<select name="external_content_id" class="form-control">
						<option value="">Select Extent Content</option>
						@foreach ($externalContents as $externalContent)
							<option value="{{$externalContent->id}}">{{$externalContent->name}} | From {{$externalContent->source}}</option>
						@endforeach
						<div><small>Use this only if you want to use system supplied questions</small></div>
					</select>
				</div>
				@endif
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