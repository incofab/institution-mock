<?php
$title = 'Register Multi Event Courses'; ?>
@extends('institutions.layout')

@section('content')
<div class="container content" id="my-licenses">  
	<div class="app-title"> 
		<ul class="app-breadcrumb breadcrumb">
			<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
			<li class="breadcrumb-item"><a href="{{instRoute('dashboard')}}">Dashboard</a></li>
			<li class="breadcrumb-item"><a href="{{instRoute('events.index')}}">Events</a></li>
			<li class="breadcrumb-item active"><a href="#">Register Event Subjects</a></li>
		</ul>
	</div>
	<div class="row justify-content-center">
		<div class="col-md-10">
        	<div class="tile">
				<div class="card">
					<div class="card-header font-weight-bold">Record Event Subjects</div>
					<div class="card-body">
						<form action="{{instRoute('event-courses.multi-store', $event)}}" method="post" autocomplete="off" >
							@csrf
							<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th><b>Subjects</b></th>
										<th><b>Session</b></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($courses as $course)
									<tr>
										<td class="py-0 pt-3">{{$course->course_code}}</td>
										<td class="py-0 pt-3">
											<input type="hidden" name="subjects[{{$course->id}}][course_id]" value="{{$course->id}}">
											<div class="form-group">
												<select name="subjects[{{$course->id}}][course_session_id]" class="form-control">
													<option value="">Select subject</option>
													@foreach ($course->courseSessions as $courseSession)
													<option value="{{$courseSession->id}}">{{$courseSession->session}}</option>
													@endforeach
												</select>
											</div>
										</td>
									</tr>
									@endforeach
								</tbody>
							</table>
							</div>
							<div>
								<button type="submit" class="btn btn-primary float-right">
									Submit
								</button>
							</div>
						</form>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
	
@stop
