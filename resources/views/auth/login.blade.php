@extends('vali_layout')

@section('body')

<section class="material-half-bg">
	<div class="cover"></div>
</section>
<section class="login-content">
	<div class="logo">
		<h1>{{config('app.name')}}</h1>
	</div>
	<div class="login-box" style="min-height: 500px;">
        <form method="POST" action="{{ route('login') }}" class="login-form">
			<h3 class="login-head mb-1 pb-2">
				<i class="fa fa-lg fa-fw fa-user"></i>SIGN IN
			</h3>
        	@include('common.message')
            @csrf
			<div class="clearfix mb-3">
				<a href="{{route('exams.view-result')}}" class="float-left">Check Result</a>
				<a href="{{config('app.exam-url')}}" class="float-right">Exam Page</a>
			</div>
            <div class="form-group">
				<label class="control-label">Email</label> 
				<input class="form-control" type="text" placeholder="email" autofocus
					name="email" value="{{old('email')}}">
				@error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
			</div>
			<div class="form-group">
				<label class="control-label">PASSWORD</label> 
				<input class="form-control" type="password" placeholder="Password"
					name="password">
				@error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
			</div>
			<div class="form-group">
				<div class="utility">
					<div class="animated-checkbox">
						<label> <input type="checkbox"><span class="label-text">Stay
								Signed in</span>
						</label>
					</div>
				</div>
			</div>
			<div class="semibold-text mb-2">
				<a href="#">Forgot Password</a> | 
				<a href="{{route('register')}}">Register</a>
			</div>
			<div class="form-group btn-container mb-2">
				<button class="btn btn-primary btn-block">
					<i class="fa fa-sign-in fa-lg fa-fw"></i>SIGN IN
				</button>
			</div>
        </form>
	</div>
</section>










@endsection
