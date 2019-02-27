@extends('frontend.default')

@section('content')

@endsection
@section('scripts')
    @include('frontend.scripts.google-recaptcha',['action'=>'contact'])
@endsection
