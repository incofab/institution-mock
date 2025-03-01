<?php
// dd(json_encode($allRecords, JSON_PRETTY_PRINT));
$title = 'Preview'; ?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Event Subjects
		</h1>
		<p>Event Subjects</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{instRoute('events.index')}}">Events</a></li>
		<li class="breadcrumb-item">Event Subjects</li>
	</ul>
</div>
<div>
	<a href="{{instRoute('event-courses.multi-create', [$event])}}" class="btn btn-primary my-2">
		Insert Multiple Subjects
	</a>
	<div class="tile">
		<h3 class="tile-title">{{$event['title']}}</h3>
		<div class="tile-body">
			<dl class="row">
				<dt class="col-md-3">Description</dt>
				<dd class="col-md-9">{{$event['description'] ?? '-'}}</dd>
				<dt class="col-md-3 mt-3">Duration</dt>
				<dd class="col-md-9 mt-3">{{$event->duration}} mins</dd>
			</dl>
		</div>
		<form action="{{instRoute('event-courses.store', [$event])}}" method="post" autocomplete="off" 
			class="border px-3 pt-2 mb-2">
			@csrf
			<div class="row">
				<div class="col-6 col-md-3">
					<div class="form-group">
						<label class="bmd-label-floating">Subject</label>
						<select name="course_id" id="select-subject" class="form-control">
							<option value="">Select subject</option>
							@foreach ($courses as $course)
							<option value="{{$course->id}}">{{$course->code}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<div class="form-group">
						<label class="bmd-label-floating">Session</label>
						<select name="course_session_id" id="course-session" class="form-control">
							<option value="">Select session</option>
						</select>
					</div>
				</div>
				<div class="col-6 col-md-3">
					<br />
					<button type="submit" class="btn btn-primary float-right">
						Submit
					</button>
				</div>
			</div>
		</form>
		<!-- End Navbar -->
		<div class="content">
			<div class="card">
				<div class="card-header card-header-primary">
					<h4 class="card-title">List Event Subjects</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						@include('common.message')
						<table class="table">
							<thead class=" text-primary">
								<tr>
									<th>Course</th>
									<th>Created At</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							@foreach($allRecords as $record)
							<?php $courseSession = $record->courseSession ?? $record->course_session; ?> 
								<tr>
									<td>{{$courseSession->course->code}} - {{$courseSession->session}}</td>
									<td>{{$record->created_at}}</td>
									<td>
										@if ($event->isNotExternal())
										<a class="text-danger" href="{{instRoute('event-courses.destroy', [1, $record])}}"
											onclick="return confirm('Delete this event subject?')">
											<i class="fa fa-trash"></i>
										</a>
										@endif
									</td>
								</tr>
							@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	const courses = {!! json_encode($courses) !!};
	// var selectedTopics = [];
	function onCourseSelected(courseId) {
		const selectedCourse = courses.find(course => course.id == courseId);
		populateCourseSessions(selectedCourse?.course_sessions ?? []);
	}
	
	function populateCourseSessions(courseSessions) {
		let options = '<option value="">Select session</option>';
		courseSessions.forEach(courseSession => {
			options += `<option value="${courseSession.id}">${courseSession.session}</option>`;
		});
		$('select#course-session').html(options);
	}
	
	$(document).on('change', '#select-subject', function() {
		onCourseSelected($(this).val());
	});
</script>
		
@endsection