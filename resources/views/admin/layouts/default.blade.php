<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport"
          content="width=device-width, initial-scale=1 maximum-scale=1, shrink-to-fit=no, user-scalable=no, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Title</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body id="backend" class="hold-transition skin-blue sidebar-mini">
<div id="app"></div>
@include('layouts.footer')
<script src="{{mix('/js/app.js')}}"></script>
</body>
</html>
