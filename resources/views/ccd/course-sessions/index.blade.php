@extends('institutions.layout')

@section('content')
@include('ccd._breadcrumb', ['headerTitle' => 'All Sessions', 
'crumbs' => [
	breadCrumb('Subjects', instRoute('ccd.courses.index', [$course?->exam_content_id])),
	breadCrumb('Sessions')->active()
]])
	<div class="tile full">
		<div class="tile-title">
			<div>
				<div class="float-left">Sessions {{'for '.$course->course_code}}</div>
				@if (!empty($course))
					<a href="{{instRoute('ccd.course-sessions.create', [$course])}}" class="btn btn-success float-right" >
						<i class="fa fa-plus"></i> New
					</a>
				@endif
				<div class="clearfix"></div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table table-striped">
				<tr>
					<th>Session</th>
					<th>Category</th>
					<th>General Instrunction</th>
					<th>Questions</th>
					<th></th>
				</tr>
				@foreach($allRecords as $record)
				<tr>
					<td>{{$record['session']}}</td>
					<td>{{$record->category}}</td>
					<td>{{$record->general_instructions}}</td>
					<td>{{$record->questions_count}}</td>
					<td>
						<a href="{{instRoute('ccd.questions.index', $record)}}" 
							class="btn btn-sm btn-link"> Questions </a>
						<a href="{{instRoute('ccd.passages.index', $record)}}" 
							class="btn btn-sm btn-link"> Passages </a>
						<a href="{{instRoute('ccd.instructions.index', $record)}}" 
							class="btn btn-sm btn-link"> Instructions </a>

						<a href="{{instRoute('ccd.questions.upload.create', $record)}}" 
							class="btn btn-sm btn-primary"> <i class="fa fa-upload"></i> </a>

						<a href="{{instRoute('ccd.course-sessions.edit', [$record->course_id, $record])}}" 
							class="btn btn-sm btn-success"> <i class="fa fa-edit"></i> </a>
						{{-- 
						<a href="{{instRoute('course-sessions.destroy', $record)}}" 
							onclick="return confirm('Are you sure?')"
							class="btn btn-sm btn-danger"> <i class="fa fa-trash"></i> </a> --}}
						@include('common._delete_form', ['deleteRoute' => instRoute('ccd.course-sessions.destroy', [$record->course_id, $record])])
					</td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
@stop
