<?php
$title = 'Institution - Extend Exam Time'; ?>
@extends('institutions.layout', ['pageTitle' => 'Admin Dashboard | Extend Exam Time'])
@section('content')
<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Exams
		</h1>
		<p>Extend Exam Time</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i> <a href="{{instRoute('dashboard')}}">Dashboard</a></li>
		<li class="breadcrumb-item">Extend Time</li>
	</ul>
</div>
@include('common.message')
<div class="tile">
    <div class="tile-header mb-3">
        Extend Exam Time
    </div>
    <div class="tile-body">
        <div>
            <div>
                <p><b>Name: </b> <span>{{$exam->student->name}}</span></p>
                <p><b>Code: </b> <span>{{$exam->exam_no}}</span></p>
                <br>
                <div>
                    <form method="POST" action="" >
                        @csrf
                        <div class="form-group">
                            <label for="">Duration (mins)</label>
                            <input type="number" name="duration" value="{{old('duration')}}" class="form-control">
                        </div>
                        <br>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary mx-auto" value="Submit">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
