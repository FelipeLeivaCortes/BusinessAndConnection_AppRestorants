@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('public/backend/assets/css/invoice.css?v=1.0') }}">

<div class="row">
	<div class="col-lg-8 offset-lg-2">
		@include('backend.user.purchase.action')
		@include('backend.user.purchase.template.default')
	</div>
</div>
@endsection