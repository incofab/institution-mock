<?php
$title = isset($title) ? $title : 'Admin'; ?>

@extends('vali_layout')

@section('body')
	
	@include('admin.header')
	
	@include('admin._sidebar')
	@include('common.message')
	
	<main class="app-content">
	@yield('content')
	</main>
@endsection


