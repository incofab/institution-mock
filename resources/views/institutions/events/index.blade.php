<?php
$title = 'Institution - All Events';
$confirmMsg = 'Are you sure?';
?>
@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Events
		</h1>
		<p>List of all Events</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Events</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
    <div class="tile-header clearfix mb-3">
    	<a href="{{instRoute('events.create')}}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> New</a>
    </div>
    <div class="tile-body">
    	<table class="table table-hover table-bordered" id="data-table" >
    		<thead>
    			<tr>
    				<th>S/No</th>
    				<th>Title</th>
    				<th>Description</th>
    				<th>Subjects</th>
    				<th>Duration</th>
    				<th><i class="fa fa-bars p-2"></i></th>
    			</tr>
    		</thead>
			@foreach($allRecords as $record)
				<tr>
					<td>{{$loop->iteration}}</td>
					<td><a href='{{instRoute('events.show', $record)}}' class='btn btn-link'>{{$record['title']}}</a></td>
					<td>{{$record['description']}}</td>
					<td>{{$record->event_courses_count}}</td>
					<td>{{$record->duration}}</td>
					<td>
						{{-- <a href='{{instRoute('event-courses.index', $record)}}' class='btn btn-link'>Subjects</a> 
						<a href='{{instRoute('exams.index', $record)}}' class='btn btn-link'>Exams</a>
						<a href='{{instRoute('events.edit', $record)}}' class='btn btn-info btn-link'><i class='fa fa-edit'></i></a>
						<a onclick='return confirmAction()' href='{{instRoute('events.destroy', $record)}}' class='btn btn-link text-danger'>
							<i class='fa fa-trash'></i>
						</a>
						--}}
						<i class="fa fa-bars p-2 pointer"
						   tabindex="0"
						   role="button" 
                           data-html="true" 
                           data-toggle="popover" 
                           title="Options" 
                           data-placement="left"
                           data-content="<div>
                            <div><small><i class='fa fa-eye'></i> 
                            	<a href='{{instRoute('event-courses.index', $record)}}' class='btn btn-link'>Subjects</a>
                            </small></div>
							{{-- 
							--}}
                            <div><small><i class='fa fa-graduation-cap'></i> 
                            	<a href='{{instRoute('exams.index', $record)}}' class='btn btn-link'>Exams</a>
                            </small></div>
                            <div><small><i class='fa fa-users'></i> 
                            	<a href='{{instRoute('exams.events.grades.create', $record)}}' class='btn btn-link'>Register Students for Exam</a>
                            </small></div>
                            
                            <div><small><i class='fa fa-edit'></i> 
                            	<a href='{{instRoute('events.edit', $record)}}' class='btn btn-link'>Edit</a>
                            </small></div>
                            {{-- 
                            <div><small><i class='fa fa-chart-bar'></i> 
                            	<a class='btn btn-link' href='{{instRoute('events.result', $record)}}' >View Result</a>
                            </small></div>
							 --}}
                            
                            <div><small><i class='fa fa-trash'></i> 
                            	<a onclick='return confirmAction()' href='{{instRoute('events.destroy', $record)}}' class='btn btn-link text-danger'>Delete</a>
                        	</small></div>
                        	</div>
                            "></i>
					</td>
				</tr>
			@endforeach
		</table>
	</div>
	<div class="tile-footer">
		@include('common.paginate')
	</div>
</div>

<script type="text/javascript">
$(function () {
//   $('[data-toggle="popover"]').popover();
  var popOverSettings = {
//	 	    placement: 'bottom',
//	 	    container: 'body',
//	 	    html: true,
		    selector: '[data-toggle="popover"]', //Sepcify the selector here
//	 	    content: function () {
//	 	        return $('#popover-content').html();
//	 	    }
	}
	
	$('#data-table').popover(popOverSettings);
});
function confirmAction() {
	return confirm('{{$confirmMsg}}');
}
</script>

@stop
