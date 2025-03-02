<?php
$title = 'Register Exam';
$subjects = [];
?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Exams
		</h1>
		<p>Register Exam</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		{{-- <li class="breadcrumb-item"><a href="{{instRoute('exams.index')}}">Exams</a></li> --}}
		<li class="breadcrumb-item">Register</li>
	</ul>
</div>
@include('common.message')
<div>
	<div class="tile">
		<h3 class="tile-title">Register Exam</h3>
		@if ($student)
		<div class="">
			<dl class="row">
				<dt class="col-sm-3">Name</dt>
				<dd class="col-sm-9">{{$student['lastname']}} {{$student['firstname']}}</dd>
				<dt class="col-sm-3">Class</dt>
				<dd class="col-sm-9">{{Arr::get($student->grade, 'title')}}</dd>
				<dt class="col-sm-3">Student ID</dt>
				<dd class="col-sm-9">{{$student['student_id']}}</dd>
			</dl>
		</div>
		@endif
		<div class="tile-body">
			@if ($event)
			<div><b>Event:</b> {{$event->title}}</div>
			<br>
			<form action="{{instRoute('exams.store')}}" method="post">
				@csrf
				@if ($student)
					<input type="hidden" name="student_id" value="{{$student->id}}">
				@else
				<div class="form-group w-75">
					<label class="control-label">Select Student</label>
					<select name="student_id" class="form-control">
						@foreach($students as $thisStudent)
						<option value="{{$thisStudent->id}}" @selected($thisStudent->id == old('student_id'))>
							{{$thisStudent->name}}
						</option>
						@endforeach
					</select>
				</div>
				@endif
				<div class="form-group w-75" >
					<label class="control-label">Subjects</label>
					<select name="course_session_ids[]" id="select-subjects" required="required" 
						class="form-control" multiple="multiple">
						<option value="">Select Subject</option>
						@foreach ($eventCourses as $eventCourse)
							<option value="{{$eventCourse->course_session_id}}">{{$eventCourse->getCourseSession()->course->course_code}}</option>
						@endforeach
					</select>
				</div>
				<div class="tile-footer">
					<input type="hidden" name="event_id" value="{{$event->id}}">
					<button class="btn btn-primary" type="submit">
						<i class="fa fa-fw fa-lg fa-check-circle"></i>Submit
					</button>
				</div>
			</form>
			@else
			<div class="form-group w-75">
				<label class="control-label">Select Event</label>
				<select id="select-event-id" name="event_id" class="form-control" >
					<option value="">Select event</option>
					@foreach($events as $thisEvent)
					<option value="{{$thisEvent->id}}" @selected($thisEvent->id == old('event_id', $event?->id))>
						{{$thisEvent->title}}
					</option>
					@endforeach
				</select>
			</div>
			@endif
		</div>
	</div>
</div>
<script type="text/javascript" src="{{assets('lib/select2.min.js')}}"></script>
<script type="text/javascript">
$('#select-subjects').select2();

$(function () {
	$('#select-event-id').on('change', function(e) {
		var selectedEventId = $(this).val();
		const url = "{{instRoute('exams.create', ['student' => $student, 'event_id' => '--'])}}";
		// var url = "{{instRoute('students.index', ['grade' => '--'])}}";
		window.location.href = url.replace('--', selectedEventId);
	});
});

// function eventSelected() {
// 	const dropdown = document.getElementById('select-event-id');
// 	const eventId = dropdown.value;
// 	if (eventId) {
// 		const url = '{{instRoute('exams.create', $student)}}'+'?event_id='+eventId;
// 		// const url = `/posts/${postId}`; // Adjust the base URL as needed
// 		window.location.href = url;
// 	}
// }



// var eventSubjects = {!!json_encode($subjects)!!};
// $(function() {
// 	eventSelected($('form select[name="event_id"]'));
// 	$('form select[name="event_id"]').on('change', function(e) {
// 		eventSelected($(this));
// 	});
// });
// function eventSelected(obj) {
// 	var eventId = obj.val();
// 	var subjects = eventSubjects[eventId];
// 	var s = '';
// 	subjects.forEach(function(subject, i) {
// 		s += '<option value="'+subject.course_session_id+'">'+subject.course.title+'</option>';
// 	});
// 	$('#select-subjects').html(s);
// 	$('form .select2-selection__rendered').html(''); // For Select 2 plugin
// }
</script>

@endsection