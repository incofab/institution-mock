@extends('institutions.layout')

@section('content')
@include('ccd._breadcrumb', [
'crumbs' => [
	breadCrumb('Subjects')->active()
]])
	<div class="tile full">
		<div class="tile-title">
			<div>
				<div class="float-left">All Subjects</div>
				<a href="{{instRoute('ccd.courses.create')}}" class="btn btn-success float-right" >
					<i class="fa fa-plus"></i> New
				</a>
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Subject</th>
					<th>Title</th>
					<th>Sessions</th>
					<th></th>
				</tr>
				@foreach($allRecords as $record)
				<tr>
					<td>{{$record->course_code}}</td>
					<td>{{$record->course_title}}</td>
					<td>{{$record->course_sessions_count}}</td>
					<td>
						<a href="{{instRoute('ccd.course-sessions.index', $record)}}" 
							class="btn btn-sm btn-link"> Sessions </a>
						<a href="{{instRoute('ccd.courses.edit', $record)}}" 
							class="btn btn-sm btn-link text-info"> <i class="fa fa-edit"></i> Edit</a>
						@include('common._delete_form', ['deleteRoute' => instRoute('ccd.courses.destroy', $record)])
					</td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
@stop
