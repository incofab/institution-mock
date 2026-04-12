@extends('vali_layout')

@section('meta')
<style>
.institution-select-page {
	min-height: 100vh;
	background: #f5f7fb;
	padding: 48px 16px;
}
.institution-select-wrap {
	max-width: 980px;
	margin: 0 auto;
}
.institution-select-hero {
	display: flex;
	justify-content: space-between;
	align-items: flex-end;
	gap: 24px;
	margin-bottom: 24px;
}
.institution-select-hero h1 {
	font-size: 28px;
	margin-bottom: 8px;
}
.institution-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
	gap: 18px;
}
.institution-card {
	background: #fff;
	border: 1px solid #dbe3ef;
	border-radius: 6px;
	padding: 22px;
	box-shadow: 0 10px 24px rgba(20, 35, 55, 0.08);
	display: flex;
	flex-direction: column;
	min-height: 210px;
}
.institution-card__icon {
	width: 46px;
	height: 46px;
	border-radius: 6px;
	display: inline-flex;
	align-items: center;
	justify-content: center;
	background: #e9f7ef;
	color: #1e7e34;
	margin-bottom: 18px;
}
.institution-card__name {
	font-size: 18px;
	font-weight: 700;
	margin-bottom: 8px;
	color: #263238;
}
.institution-card__meta {
	color: #6c757d;
	font-size: 13px;
	margin-bottom: 20px;
}
.institution-card__actions {
	margin-top: auto;
	display: flex;
	justify-content: space-between;
	align-items: center;
	gap: 12px;
}
.institution-current-badge {
	border-radius: 6px;
	padding: 6px 10px;
	background: #fff4e5;
	color: #8a5a00;
	font-size: 12px;
	font-weight: 700;
}
@media (max-width: 767px) {
	.institution-select-page {
		padding: 28px 12px;
	}
	.institution-select-hero {
		display: block;
	}
	.institution-select-hero h1 {
		font-size: 24px;
	}
	.institution-select-hero .btn {
		margin-top: 16px;
	}
}
</style>
@endsection

@section('body')
<section class="institution-select-page">
	<div class="institution-select-wrap">
		@include('common.message')

		<div class="institution-select-hero">
			<div>
				<p class="text-uppercase text-success font-weight-bold mb-2">Institution access</p>
				<h1>Select an institution</h1>
				<p class="text-muted mb-0">
					Choose the institution you want to work with now.
				</p>
			</div>
			<a class="btn btn-outline-secondary" href="{{route('logout')}}">
				<i class="fa fa-sign-out-alt"></i> Logout
			</a>
		</div>

		<div class="institution-grid">
			@foreach($institutionUsers as $institutionUser)
				@php($institution = $institutionUser->institution)
				<div class="institution-card">
					<div class="institution-card__icon">
						<i class="fa fa-school"></i>
					</div>
					<div class="institution-card__name">{{$institution->name}}</div>
					<div class="institution-card__meta">
						Code: {{$institution->code}}
						@if($institutionUser->role)
							<br>Role: {{ucfirst($institutionUser->role->value)}}
						@endif
					</div>
					<div class="institution-card__actions">
						@if($selectedInstitution?->id === $institution->id)
							<span class="institution-current-badge">Current</span>
						@else
							<span></span>
						@endif
						<form action="{{route('users.institutions.switch')}}" method="post" class="mb-0">
							@csrf
							<input type="hidden" name="institution_id" value="{{$institution->id}}">
							<button class="btn btn-primary" type="submit">
								Use Institution
							</button>
						</form>
					</div>
				</div>
			@endforeach
		</div>
	</div>
</section>
@endsection
