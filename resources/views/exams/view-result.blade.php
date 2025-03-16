<?php
$title = 'View Single Result';
$event = $exam->event;
$student = $exam->student;
?>

@extends('vali_layout') 

@section('body')

<br><br>
<div id="result-div" class="mx-auto border-left border-right px-3 px-md-5 py-3 bg-white shadow" style="max-width: 800px; min-height: 720px;">
    <section class="result-header mt-3">
    	<div class="row py-2">
    		<div class="col-2 col-md-1">
            	<div class="d-inline-block" style="width: 80px;">
            		<img src="{{$event->title}}" alt="" class="img-fluid" />
            	</div>
    		</div>
    		<div class="col text-center" style="margin-right: 0px;">
        		<div class="h5 text-truncate">{{$event->title}}</div>
            	{{-- <div class="h5 fw-bold">({{$exam->examCourses->count() . ' Subjects'}})</div> --}}
				<div class="fs-2 fw-bold">Student Assessment</div>
    		</div>
    		<div class="col-2 col-md-1"></div>
    	</div>
    	<hr class="line my-0 py-0" />
    	<div class="text-center h5 fw-bold mt-3">{{$exam->student?->name}}</div>
    </section>
	<br><br>
    <section class="mb-2">
    	<dl class="row">
    		<dt class="col-sm-3 mb-sm-3">Name</dt>
    		<dd class="col-sm-9 mb-3">{{$exam->student->name ?? 'Guest'}}</dd>
    		
    		<dt class="col-sm-3 mb-sm-3">Exam Date</dt>
    		<dd class="col-sm-9 mb-3">{{$exam->start_time->toDayDateTimeString()}}</dd>
    
    		<dt class="col-sm-3 mb-sm-3">Exam Duration</dt>
    		<dd class="col-sm-9 mb-3">{{$exam->duration}} mins</dd>

    		<dt class="col-sm-3 mb-sm-3">Exam Score</dt>
    		<dd class="col-sm-9 mb-3">{{$exam->scorePercentSum()}}/{{$exam->examCourses->count() * 100}}</dd>
    	</dl>
    </section>
    
    <section class="">
    	<div class="table-responsive">
    		<table class="table result-table">
    			<thead>
    				<tr>
    					<th>Subject</th>
    					<th>Session</th>
    					<th>Score</th>
    					<th>Score %</th>
    				</tr>
    			</thead>
    			<tbody>
    				@foreach($exam->examCourses as $examCourse)
    				<tr>
    					<td>{{$examCourse->course_code}}</td>
						<td>{{$examCourse->session}}</td>
    					<td>{{$examCourse->score}}/{{$examCourse->num_of_questions}}</td>
    					<td>{{$examCourse->scorePercent()}}</td>
    				</tr>
    				@endforeach
<?php $totalScorePercent = $exam->examCourses->sum(
  fn($item) => $item->scorePercent(),
); ?>
    				<tr class="font-weight-bold">
    					<td colspan="{{!$exam->event ? 2 : 1}}"><b>Total</b></td>
						<td></td>
    					<td>{{$exam->score}}/{{$exam->num_of_questions}}</td>
    					<td>{{round($totalScorePercent, 2)}}/{{$exam->examCourses->count() * 100}}</td>
    				</tr>
    			</tbody>
    		</table>
    	</div>
    </section>
</div>
<div id="result-div-cover"></div>
<br>
<style>
#result-div .result-table th,
#result-div .result-table td{
	padding-top: 10px;
	padding-bottom: 10px;
}
#result-div-cover{
    content: '';
    display: block;
    position: absolute;
    top:0; bottom: 0; left:0; right: 0;
    background-image: url("{{assets('img/logo.webp')}}");
    background-repeat: repeat;
    opacity: 0.02;
    background-size: auto;
    z-index: -2;
}
</style>

{{--
<style>
body{background: #eeeeee; }
#result{background: #fff; }
hr.line{    
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
}
</style>
<div id="result" class="container-fluid card mx-auto w-75 mt-2">
	<h4 class="title text-center mt-2 py-3 bg-light">{{$event->institution->name}}</h4>
	<hr class="line" />
	<h5 class="title mt-1" align="right"><i>{{$event->title}}</i></h5>
	<hr class="line" />
	<h6 class="title mt-3 bg-info p-1"><i>Personal Details</i></h6>
	<hr class="line" />
	<div class="user-details mb-2">
		<dl class="row">
			<dt class="col-3 col-sm-2 mb-3">Fullname</dt>
			<dd class="col-9 col-sm-10 mb-3">{{$student->firstname}} {{$student->lastname}}</dd>
			<dt class="col-3 col-sm-2 mb-3">Student ID</dt>
			<dd class="col-9 col-sm-10 mb-3">{{$student->code}}</dd>
			<dt class="col-3 col-sm-2 mb-3">Exam No</dt>
			<dd class="col-9 col-sm-10 mb-3">{{$exam->exam_no}}</dd>
			<dt class="col-3 col-sm-2 mb-3">Subjects</dt>
			<dd class="col-9 col-sm-10 mb-3">{{implode(', ', $exam->examCourses->map(fn($item) => $item->course_code)->toArray())}}</dd>
			<dt class="col-3 col-sm-2 mb-3">Total Score</dt>
			<dd class="col-9 col-sm-10 mb-3">{{$exam->scorePercent()}}</dd>
		</dl>
	</div>
	<hr class="line" />
	<h6 class="title m-0 bg-info p-1"><i>Result Details</i></h6>
	<hr class="line" />
	<div class="result-details">
		<ul class="list-group">
			@foreach($exam->examCourses as $examCourse)
			<li
				class="list-group-item d-flex justify-content-between align-items-center">
				{{$examCourse->course_code}} 
				<span class="badge badge-primary badge-pill">
					{{$examCourse->scorePercent()}}
				</span>
			</li>
			@endforeach
		</ul>
		<br />
	</div>
<br />
</div>
<br />
<br />
<!-- The javascript plugin to display page loading on top-->
<script type="text/javascript">

</script>
--}}
@endsection
