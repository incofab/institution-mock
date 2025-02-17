<?php
$title = 'Institution - All Registered Exams';
$confirmMsg = 'Are you sure?';

// dDie($allRecords->toArray());
?>
@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Exams
		</h1>
		<p>List of Student Exams</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Exams</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
    <div class="tile-header clearfix mb-3">
    	<div class="form-group row float-right">
			<label for="select-grade" class="col-sm-5 col-form-label">Select Event</label>
			<div class="col-sm-7">
				<select name="event_id" id="select-event" class="form-control">
					@foreach($allEvents as $thisEvent)
						<option value="{{$thisEvent->id}}" @selected($thisEvent->id == $event->id)
							title="{{$thisEvent->description}}" >{{$thisEvent->title}}
						</option>
					@endforeach
				</select>
			</div>
		</div>
    </div>
    <div class="tile-body">
		<div class="mb-2"><b>Event: </b> {{$event->title}}</div>
    	<table class="table table-hover table-bordered" id="data-table" >
    		<thead>
    			<tr>
    				<th>Student Name</th>
    				<th>Exam No</th>
    				<th>Event</th>
     				<th>Subjects</th>
    				<th>Duration</th>
    				<th>Status</th>
    				<th><i class="fa fa-bars p-2"></i></th>
    			</tr>
    		</thead>
			@foreach($allRecords as $record)
			<?php $student = $record['student']; ?>
				<tr>
					<td>{{$student->name}}</td>
					<td>{{$record['exam_no']}}</td>
					<td>
						<a href="{{instRoute('event-courses.index', [$event->id])}}" 
							class="btn-link">{{$event['title']}}</a>
					</td>
					<td>{{implode(', ', $record->examCourses->map(fn($item) => $item->course_code)->toArray())}}</td>
					<td>{{$event['duration']}} mins</td>
					<td>
						<button class="btn btn-primary">{{$record['status']}}</button>
						{{-- @if($record['status'] == 'active')
							@if(empty($record['start_time']))
								<button class="btn btn-success">Ready</button>
							@else
								<button class="btn btn-success">{{$record['status']}}</button>
							@endif
						@elseif($record['status'] == STATUS_PAUSED)
						<button class="btn btn-warning">{{$record[STATUS]}}</button>						
						@elseif($record['status'] == STATUS_SUSPENDED)
						<button class="btn btn-danger">{{$record['status']}}</button>						
						@else
						<a href="{{instRoute('home.exams.view-result', [$examNo])}}" class="btn btn-link">View Results</a>
						@endif --}}
					</td>
					<td>
						@include('common._delete_form', ['deleteRoute' => instRoute('exams.destroy', $record)])
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
// 	    placement: 'bottom',
// 	    container: 'body',
// 	    html: true,
	    selector: '[data-toggle="popover"]', //Sepcify the selector here
// 	    content: function () {
// 	        return $('#popover-content').html();
// 	    }
	}
	
	$('#data-table').popover(popOverSettings);
	
	$('#select-event').on('change', function(e) {
		var url = "{{instRoute('exams.index', '--')}}";
		var selectedEventId = $(this).val();
		if(selectedEventId){
			window.location.href = url.replace('--', selectedEventId);
			return;
		}
	});
});

function confirmAction() {
	return confirm('{{$confirmMsg}}');
}
</script>

@stop
