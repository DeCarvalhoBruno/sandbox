@extends('frontend.default')
@section('content')
    <div class="row mt-4">
        <full-page-search initial-value="{{$q}}"></full-page-search>
    </div>
@endsection
