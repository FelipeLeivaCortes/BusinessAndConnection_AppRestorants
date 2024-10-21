@extends('layouts.pos')

@section('content')

@include('backend.user.pos.ajax.active-orders')

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/pos.js') }}"></script>
@endsection
