<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="page-id" content="{{ get_page_id() }}">

    <title>{{$title}}</title>
    <link href="{{ mix('css/app.css','6aa0e') }}" rel="stylesheet">
</head>
<body>
<div id="app">
        <div id="content_container">
        @yield('content')
    </div>
</div>

<!-- Scripts -->
@include('partials.javascript_footer')
<script src="{{ mix('js/app.js','6aa0e') }}"></script>
@yield('scripts')
</body>
</html>
