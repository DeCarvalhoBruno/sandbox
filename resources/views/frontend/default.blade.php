<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1 maximum-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="page-id" content="{{ get_page_id() }}">

    <title>{{$title}}</title>
    <link href="{{ mix('css/app.css','6aa0e') }}" rel="stylesheet">
</head>
<body>
@include('partials.header')
<div id="app">
    <div id="content_container" class="container">
        @if(isset($breadcrumbs))
            <div id="breadcrumb-wrapper" class="col">
                <div class="card">
                    {!! $breadcrumbs !!}
                </div>
            </div>
        @endif
        @yield('content')
    </div>
    @include('partials.footer')
    <a href="#" id="scroll-up" style="display: none;">
        <fa class="fa-2x" icon="angle-up"></fa>
    </a>
</div>
<!-- Scripts -->
@yield('scripts')
@include('partials.javascript_footer')
<script src="{{ mix('js/app.js','6aa0e') }}"></script>
</body>
</html>
