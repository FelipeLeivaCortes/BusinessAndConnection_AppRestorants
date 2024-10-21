@extends('layouts.pos')

@section('content')

@include('backend.user.pos.ajax.table')

@endsection

@section('js-script')
<script src="{{ asset('public/backend/assets/js/pos.js') }}"></script>
@endsection
