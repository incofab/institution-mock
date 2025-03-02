@extends('institutions.layout')

@section('content')

<div>
	@include('ccd._breadcrumb', [
		'crumbs' => [
	breadCrumb('Subjects', instRoute('ccd.courses.index')),
	breadCrumb('Create Subject')->active()
]])
	<div class="justify-content-center">
    	<div class="tile">
			<div class="tile-title">{{$edit ? 'Update' : 'Create'}} Session</div>
			<form method="POST" action="{{$edit ? instRoute('ccd.courses.update', [$edit]) : instRoute('ccd.courses.store')}}" name="register" >
				@include('common.message')
				@csrf
				@if ($edit)
					@method('PUT')
				@endif
				<div class="form-group">
					<label for="" >Subject Name</label><br />
					<input type="text" name="course_code" value="{{old('course_code',$edit?->course_code)}}"  class="form-control" />
				</div>
				
				<div class="form-group">
					<label for="" >Title [optional]</label>
					<textarea name="course_title" id="" rows="4" class="form-control" 
						>{{old('course_title', $edit?->course_title)}}</textarea>
				</div>
				
				<div class="form-group">
					<input type="submit" name="add" style="width: 60%; margin: auto;" 
							class="btn btn-primary btn-block" value="Submit">
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
</div>

@endsection