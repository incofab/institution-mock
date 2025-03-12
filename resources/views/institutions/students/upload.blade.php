<?php
$title = 'Upload Student - Institution';
$subjects = [];
?>

@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Students
		</h1>
		<p>Upload students record from Excel</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item"><a href="{{instRoute('students.index')}}">Students</a></li>
		<li class="breadcrumb-item">Upload Students</li>
	</ul>
</div>
@include('common.message')
<div>
	<div class="tile">
		<h3 class="tile-title">Upload Students</h3>
		<div class="clearfix">
        	<a href="{{instRoute('students.download-template')}}" class="btn btn-primary float-left">
        		<i class="fa fa-download"></i> Download Sample File
        	</a>
		</div>
    	<div class="alert alert-info my-2">
    		<strong>Available Classes:</strong><br />
    		<div>{{implode(' | ', array_column($grades->toArray()??[], 'title'))}}</div>
    	</div>
		<form action="{{instRoute('students.upload.store')}}" method="post" enctype="multipart/form-data" >
    		@csrf
    		<div class="tile-body">
    			<div class="form-group w-75">    		
    				<label for="" >Excel Student Records</label><br />
    				<input type="file" class="form-control" name="file" value="" />
    				<input type="hidden" class="form-control" name="upload_students" value="true" />
	    		</div>
    		</div>
    		<div class="tile-footer clearfix">
    			<button class="btn btn-primary float-right" type="submit" 
    				onclick="return confirm('Note: This might a few minutes and should not be interrupted. \n\nContinue?')" >
    				<i class="fa fa-fw fa-lg fa-check-circle"></i> Upload
    			</button>
    		</div>
		</form>
	</div>

</div>

@endsection