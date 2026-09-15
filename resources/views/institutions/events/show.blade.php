<?php $title = 'Event Details'; ?>

@extends('institutions.layout')

@section('content')
<div class="app-title">
  <div><h1><i class="fa fa-calendar"></i> Event Details</h1></div>
  <ul class="app-breadcrumb breadcrumb">
    <li class="breadcrumb-item"><a href="{{instRoute('events.index')}}">Events</a></li>
    <li class="breadcrumb-item">{{$event->title}}</li>
  </ul>
</div>
<div class="tile">
  <h3 class="tile-title">{{$event->title}}</h3>
  <dl class="row">
    <dt class="col-sm-3">Description</dt><dd class="col-sm-9">{{$event->description ?? '-'}}</dd>
    <dt class="col-sm-3">Duration</dt><dd class="col-sm-9">{{$event->duration}} mins</dd>
    <dt class="col-sm-3">Status</dt><dd class="col-sm-9">{{$event->status}}</dd>
  </dl>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead><tr><th>Subject</th><th>Session</th></tr></thead>
      <tbody>
        @forelse ($eventCourses as $eventCourse)
          <?php $session = $eventCourse->getCourseSession(); ?>
          <tr><td>{{$session?->course?->course_code ?? 'Unavailable'}}</td><td>{{$session?->session ?? '-'}}</td></tr>
        @empty
          <tr><td colspan="2">No subjects have been added to this event.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
