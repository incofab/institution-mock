@extends('institutions.layout')

@section('content')

<div class="app-title">
	<div>
		<h1>
			<i class="fa fa-dashboard"></i> Dashboard
		</h1>
		<p>Administrative interface to manage exams and register students</p>
	</div>
	<ul class="app-breadcrumb breadcrumb">
		<li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
		<li class="breadcrumb-item"><a href="#">Dashboard</a></li>
	</ul>
</div>
@include('common.message')
<div class="tile">
	<h3 class="tile-title">Start running exams</h3>
	<div class="tile-body">
		<p class="mb-3">
			Follow these steps in order. You can come back here anytime.
		</p>
		<div class="table-responsive">
			<table class="table mb-3">
				<tr>
					<td><strong>1. Add classes</strong><br><span class="small">Create the class groups your students belong to.</span></td>
					<td class="text-right"><a href="{{instRoute('grades.create')}}" class="btn btn-sm btn-primary">Add Class</a></td>
				</tr>
				<tr>
					<td><strong>2. Add students</strong><br><span class="small">Add one student or upload many at once.</span></td>
					<td class="text-right"><a href="{{instRoute('students.create')}}" class="btn btn-sm btn-primary">Add Student</a></td>
				</tr>
				<tr>
					<td><strong>3. Add subjects</strong><br><span class="small">Create subjects and add question sessions.</span></td>
					<td class="text-right"><a href="{{instRoute('ccd.courses.create')}}" class="btn btn-sm btn-primary">Add Subject</a></td>
				</tr>
				<tr>
					<td><strong>4. Create exam</strong><br><span class="small">Create an event, attach subjects, then register students.</span></td>
					<td class="text-right"><a href="{{instRoute('events.create')}}" class="btn btn-sm btn-primary">Create Event</a></td>
				</tr>
			</table>
		</div>
		@if($licenses_count < 1)
			<div class="alert alert-info mb-0">
				You will need licenses before students can start exams.
				<a href="{{instRoute('fund-licenses.create')}}">Fund licenses</a>
			</div>
		@endif
		@if($unactivated_exams_count > 0)
			<div class="alert alert-warning mt-3 mb-0 d-flex justify-content-between align-items-center flex-wrap">
				<div>
					<b>{{$unactivated_exams_count}}</b> exam(s) are awaiting activation.
					@if($pending_licenses_count > 0)
						You need <b>{{$pending_licenses_count}}</b> additional license(s).
					@else
						Your current license balance can cover them.
					@endif
				</div>
				<a href="{{instRoute('invoices.unactivated-exams')}}" class="btn btn-sm btn-warning mt-2 mt-md-0">
					<i class="fa fa-file-invoice"></i> Download Invoice
				</a>
			</div>
		@endif
	</div>
</div>
<div class="row">
	<div class="col-sm-3">
		<div class="widget-small primary coloured-icon">
			<i class="icon fa fa-users fa-3x"></i>
			<div class="info">
				<h4>Students</h4>
				<p>
					<b>{{$students_count}}</b>
				</p>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="widget-small info coloured-icon">
			<i class="icon fa fa-calendar-day fa-3x"></i>
			<div class="info">
				<h4>Events</h4>
				<p>
					<b>{{$events_count}}</b>
				</p>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="widget-small warning coloured-icon">
			<i class="icon fa fa-book fa-3x"></i>
			<div class="info">
				<h4>Subjects</h4>
				<p>
					<b>{{$courses_count}}</b>
				</p>
			</div>
		</div>
	</div>
	<div class="col-sm-3">
		<div class="widget-small danger coloured-icon">
			<i class="icon fa fa-id-card fa-3x"></i>
			<div class="info">
				<h4>Licenses</h4>
				<p>
					<b>{{$licenses_count}}</b>
				</p>
			</div>
		</div>
	</div>
	<!-- 
	<div class="col-md-6 col-lg-3">
		<div class="widget-small danger coloured-icon">
			<i class="icon fa fa-star fa-3x"></i>
			<div class="info">
				<h4>Stars</h4>
				<p>
					<b>500</b>
				</p>
			</div>
		</div>
	</div>
	 -->
	
</div>
<!-- 
<div class="row">
	<div class="col-md-6">
		<div class="tile">
			<h3 class="tile-title">Features</h3>
			<ul>
				<li>Built with Bootstrap 4, SASS and PUG.js</li>
				<li>Fully responsive and modular code</li>
				<li>Seven pages including login, user profile and print friendly
					invoice page</li>
				<li>Smart integration of forgot password on login page</li>
				<li>Chart.js integration to display responsive charts</li>
				<li>Widgets to effectively display statistics</li>
				<li>Data tables with sort, search and paginate functionality</li>
				<li>Custom form elements like toggle buttons, auto-complete, tags
					and date-picker</li>
				<li>A inbuilt toast library for providing meaningful response
					messages to user's actions</li>
			</ul>
			<p>Vali is a free and responsive admin theme built with Bootstrap 4,
				SASS and PUG.js. It's fully customizable and modular.</p>
			<p>
				Vali is is light-weight, expendable and good looking theme. The
				theme has all the features required in a dashboard theme but this
				features are built like plug and play module. Take a look at the <a
					href="http://pratikborsadiya.in/blog/vali-admin" target="_blank">documentation</a>
				about customizing the theme colors and functionality.
			</p>
			<p class="mt-4 mb-4">
				<a class="btn btn-primary mr-2 mb-2"
					href="http://pratikborsadiya.in/blog/vali-admin" target="_blank"><i
					class="fa fa-file"></i>Docs</a><a class="btn btn-info mr-2 mb-2"
					href="https://github.com/pratikborsadiya/vali-admin"
					target="_blank"><i class="fa fa-github"></i>GitHub</a><a
					class="btn btn-success mr-2 mb-2"
					href="https://github.com/pratikborsadiya/vali-admin/archive/master.zip"
					target="_blank"><i class="fa fa-download"></i>Download</a>
			</p>
		</div>
	</div>
	<div class="col-md-6">
		<div class="tile">
			<h3 class="tile-title">Compatibility with frameworks</h3>
			<p>This theme is not built for a specific framework or technology
				like Angular or React etc. But due to it's modular nature it's very
				easy to incorporate it into any front-end or back-end framework
				like Angular, React or Laravel.</p>
			<p>
				Go to <a href="http://pratikborsadiya.in/blog/vali-admin"
					target="_blank">documentation</a> for more details about
				integrating this theme with various frameworks.
			</p>
			<p>
				The source code is available on GitHub. If anything is missing or
				weird please report it as an issue on <a
					href="https://github.com/pratikborsadiya/vali-admin"
					target="_blank">GitHub</a>. If you want to contribute to this
				theme pull requests are always welcome.
			</p>
		</div>
	</div>
</div>
 -->
 
@endsection
