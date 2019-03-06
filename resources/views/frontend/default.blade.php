<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{$title}}</title>
    <meta name="title" content="{{$title}}">
    <meta name="referrer" content="always">
    <meta name="viewport"
          content="width=device-width, initial-scale=1 maximum-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <link rel="search" type="application/opensearchdescription+xml" title="{{config('app.name')}}" href="/osd.xml">
    <link rel="canonical" href="{{ url()->current() }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="page-id" content="{{ get_page_id() }}">
    @if(isset($meta_robots))
        <meta name="robots" content="{{$meta_robots}}">
        @endif
    @if(isset($meta_description))
        <meta name="description" content="{{$meta_description}}">
    @endif
    @if(isset($meta_facebook))
        {!! $meta_facebook !!}
    @endif
    @if(isset($meta_twitter))
        {!! $meta_twitter !!}
    @endif
    @if(isset($meta_jsonld))
        {!! $meta_jsonld !!}
    @endif
    <link href="{{ mix('css/app.css','6aa0e') }}" rel="stylesheet">
</head>
<body>
@include('partials.header')

<div id="app">
    <div id="content_container" class="container p-0">
        @if(isset($breadcrumbs))
            <div id="breadcrumb-wrapper" class="col p-0">
                <div class="card">
                    {!! $breadcrumbs !!}
                </div>
            </div>
        @endif
        @yield('content')
    </div>
    <a href="#" id="scroll-up" style="display: none;">
        <i class="fa fa-2x fa-angle-up"></i>
    </a>
</div>
@include('partials.footer')
<!-- Scripts -->
@yield('scripts')
@include('partials.javascript_footer')
@if (app()->environment()==='production')
    <script src="{{ mix('js/manifest.js','6aa0e') }}"></script>
    <script src="{{ mix('js/vendor.js','6aa0e') }}"></script>
    <script src="{{ mix('js/app.js','6aa0e') }}"></script>
@else
    <script src="{{ mix('js/app.js','6aa0e') }}"></script>
@endif
<script src="https://smartlock.google.com/client" async defer></script>

</body>
</html>
