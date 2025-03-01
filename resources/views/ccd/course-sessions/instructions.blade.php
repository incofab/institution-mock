@extends('institutions.layout')

@section('content')

<div>
	@include('ccd._breadcrumb', [
		'headerTitle' => 'Instructions',
		'crumbs' => [
			breadCrumb('Sessions', instRoute('ccd.course-sessions.index', [$courseSession->course_id])),
			breadCrumb('Instructions')->active()
		]
	])
	<div>
		<div class="justify-content-center">
			<div class="tile">
				<div class="tile-title">{{$edit ? 'Update' : 'Create'}} Instructions</div>
				<form method="POST" action="{{$edit ? instRoute('ccd.instructions.update', [$edit]) : instRoute('ccd.instructions.store', [$courseSession])}}" >
					@include('common.message')
					@csrf
					@if ($edit)
						@method('PUT')
					@endif
					<div class="font-weight-bold">
						<div>
							<span>Course: </span>
							<span class="ml-2">{{$courseSession->course->code}}</span>
						</div>
						<div class="mt-1">
							<span>Session: </span>
							<span class="ml-2">{{$courseSession->session}}</span>
						</div>
					</div>
					<hr class="my-2">
					<div class="form-group">
						<label for="" >Instruction</label>
						<textarea name="instruction" id="" rows="4" class="form-control" 
							>{{old('instruction', $edit?->instruction)}}</textarea>
					</div>
					<div class="row">
						<div class="col-6">
							<div class="form-group">
								<label for="" >From</label><br />
								<input type="number" name="from" value="{{old('from', $edit?->from)}}"  class="form-control" />
							</div>
						</div>
						<div class="col-6">
							<div class="form-group">
								<label for="" >To</label><br />
								<input type="number" name="to" value="{{old('to', $edit?->to)}}"  class="form-control" />
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<input type="submit" name="add" style="width: 60%; margin: auto;" 
								class="btn btn-primary btn-block" value="{{empty($edit) ? 'Add' : 'Update'}}">
						<div class="clearfix"></div>
					</div>
				</form>
			</div>
		</div>
		<div>
			<div><strong class="text-md">All Instructions</strong></div>
			@foreach ($allRecords as $record)
				<div class="tile full mt-1">
					<div>{{$record->instruction}}</div>
					<div>From: {{$record->from}} - To: {{$record->to}}</div>
					<hr class="my-1">
					<div>
						<a href="{{instRoute('ccd.instructions.index', [$courseSession, $record])}}" class="btn btn-sm btn-primary">Edit</a>
						<a href="{{instRoute('ccd.instructions.destroy', [$record])}}" 
							onclick="return confirm('Are you sure?')"
							class="btn btn-sm btn-danger">Delete</a>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</div>

@endsection