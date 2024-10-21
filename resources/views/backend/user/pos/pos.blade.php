@extends('layouts.pos')

@section('content')

@include('backend.user.pos.ajax.pos')

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/pos.js') }}"></script>
@endsection

