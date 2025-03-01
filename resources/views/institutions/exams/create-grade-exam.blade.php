<?php
$title = 'Register Exam for Class';
$subjects = [];
?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Exams
		</h1>
		<p>Register Exam for Selected Class</p>
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
		<h3 class="tile-title">Register Exam For Class</h3>
		<h4 class="my-3">Event: {{$event->title}}</h4>
		<form action="{{instRoute('exams.events.grades.store', $event)}}" method="post">
    		@csrf
    		<div class="tile-body">
				<div class="form-group w-75" >
					<label class="control-label">Subjects</label>
					<select name="course_session_ids[]" id="select-subjects" required="required" 
						class="form-control" multiple="multiple" >
						<option value="">Select Subject</option>
						@foreach ($eventCourses as $eventCourse)
							<option value="{{$eventCourse->course_session_id}}">{{$eventCourse->getCourseSession()->course->code}}</option>
						@endforeach
					</select>
				</div>
				
				<div class="form-group">
					<label class="control-label">Class</label> 
					<select name="grade_id" id="select-grade" class="form-control">
    					<option value="">Select Class</option>
    					@foreach($grades as $grade)
    						<option value="{{$grade->id}}" @selected($grade->id == old('grade_id'))
    						title="{{$grade->description}}" >{{$grade->title}}</option>
    					@endforeach
					</select>
				</div>
    		</div>
    		<div class="tile-footer">
    			<button class="btn btn-primary" type="submit">
    				<i class="fa fa-fw fa-lg fa-check-circle"></i>Register
    			</button>
    		</div>
		</form>
	</div>
</div>
<script type="text/javascript" src="{{assets('lib/select2.min.js')}}"></script>
<script type="text/javascript">
$('#select-subjects').select2();
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