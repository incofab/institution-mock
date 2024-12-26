<?php
$title = isset($title) ? $title : config('app.name'); ?>

@extends('vali_layout')

@section('body')
	
	@include('institutions.header')
	
	@include('institutions._sidebar')
	
	<main class="app-content">
		@yield('content')
	</main>
	
@endsection


