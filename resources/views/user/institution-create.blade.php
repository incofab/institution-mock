@extends('vali_layout')

@section('body')

<section class="material-half-bg">
	<div class="cover"></div>
</section>
<section class="login-content">
	<div class="logo">
		<h1><?php echo config('app.name'); ?></h1>
	</div>
	<div class="login-box" style="min-height: 520px;">
		<form class="login-form" action="{{route('users.institutions.store')}}" method="post" autocomplete="off">
			@csrf
			@include('common.message')
			<h3 class="login-head">
				<i class="fa fa-lg fa-fw fa-university"></i> Create Institution
			</h3>
			<p class="text-center text-muted mb-3">
				This creates your school workspace. License cost is handled by the platform.
			</p>
			<div class="form-group">
				<label class="control-label">Institution Name</label>
				<input type="text" name="name" value="{{old('name')}}"
					placeholder="e.g. Bright Future School" class="form-control">
			</div>
			<div class="form-group">
				<label class="control-label">Email [Optional]</label>
				<input type="email" name="email" value="{{old('email')}}"
					placeholder="Email" class="form-control">
			</div>
			<div class="form-group">
				<label class="control-label">Phone [Optional]</label>
				<input type="text" name="phone" value="{{old('phone')}}"
					placeholder="Reachable mobile number" class="form-control">
			</div>
			<div class="form-group">
				<label class="control-label">Address [Optional]</label>
				<textarea class="form-control" rows="3" name="address"
					placeholder="Address of the Institution">{{old('address')}}</textarea>
			</div>
			<div class="form-group btn-container">
				<button class="btn btn-primary btn-block" type="submit">
					<i class="fa fa-sign-in fa-lg fa-fw"></i>Create and continue
				</button>
			</div>
			<div class="form-group text-center">
				<a href="{{route('users.dashboard')}}">Back to Dashboard</a>
			</div>
		</form>
	</div>
</section>

@endsection
