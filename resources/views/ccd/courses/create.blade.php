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
					<input type="text" name="code" value="{{old('code',$edit?->code)}}"  class="form-control" />
				</div>
				
				<div class="form-group">
					<label for="" >Description [optional]</label>
					<textarea name="description" id="" rows="4" class="form-control" 
						>{{old('description', $edit?->description)}}</textarea>
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