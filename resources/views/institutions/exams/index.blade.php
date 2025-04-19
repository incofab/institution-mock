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
		<a href="{{instRoute('events.download', [$event])}}" class="btn btn-primary btn-sm float-left"
			onclick="return confirm('Download result records')">
			<i class="fa fa-download"></i> Download
		</a>
		<a href="{{instRoute('events.evaluate', [$event])}}" class="btn btn-danger btn-sm float-right"
			onclick="return confirm('This wil end all ongoing exams in this event. Do you want to continue?')">
			<i class="fa fa-check"></i> Evaluate
		</a>
    	{{-- <div class="form-group row float-right m-0">
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
		</div> --}}
    </div>
    <div class="tile-body">
		<div class="mb-2"><b>Event: </b> {{$event->title}}</div>
		<div>
			<p><b>All Exams: </b> {{$allExamsCount}}</p>
			<p><b>Conducted Exams: </b> {{$startedExamsCount}}</p>
			<p><b>Pending Exams: </b> {{$pendingExamsCount}}</p>
		</div>
    	<table class="table table-hover table-bordered" id="data-table" >
    		<thead>
    			<tr>
    				<th>Student Name</th>
    				<th>Exam No</th>
    				{{-- <th>Event</th> --}}
     				<th>Subjects</th>
    				<th>Duration</th>
    				<th>Status</th>
    				<th><i class="fa fa-bars p-2"></i></th>
    			</tr>
    		</thead>
			@foreach($allRecords as $record)
			<?php
   $examFileData = $record->isActive()
     ? \App\Helpers\ExamHandler::make()->getExamFileData($record->exam_no)
     : null;
   $isOngoing = $record->isOngoing($examFileData);
   $student = $record['student'];
   ?>
				<tr>
					<td>{{$student?->name}}</td>
					<td>{{$record['exam_no']}}</td>
					{{-- <td>
						<a href="{{instRoute('event-courses.index', [$event->id])}}" 
							class="btn-link">{{$event['title']}}</a>
					</td> --}}
					<td><small>{{implode(', ', $record->examCourses->map(fn($item) => $item->course_code . ($record->isEnded() ? " = {$item->score}/{$item->num_of_questions}" : ''))->toArray())}}</small></td>
					<td>{{$event['duration']}} mins</td>
					<td class="text-center">
						@if($record->isActive() || $record->isPending())
							<button class="btn btn-sm btn-success">{{$record->status}}</button>
						@elseif($record->isEnded())
							<button class="btn btn-sm btn-danger">{{$record->status}}</button>						
							<a href="{{route('exams.view-result', [$record->exam_no])}}" class="btn btn-link">View Results</a>
						@else
							<button class="btn btn-sm btn-primary">{{$record->status}}</button>
						@endif
					</td>
					<td>
                        @if($record->canExtendTime())
                        <a href="{{instRoute('exams.extend-time', [$record->id])}}" class="btn btn-primary btn-sm mt-2">
                            <i class="fa fa-clock"></i> Extend Time
                        </a>
                        @endif
                        @if($isOngoing || $record->canExtendTime())
                        <a href="{{instRoute('exams.evaluate', $record->id)}}" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Do you want to submit this exam?')">
                            <i class="fa fa-reload"></i> Evaluate
                        </a>
                        @endif
						@include('common._delete_form', ['deleteRoute' => instRoute('exams.destroy', $record->id)])
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
