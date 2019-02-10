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
<div class="topbar">
    <div class="container d-flex">
        <nav class="nav nav-lang ml-auto">
            <a class="nav-link" href="javascript:void(0)">EN</a>
            <a class="nav-link pipe">|</a>
            <a class="nav-link" href="javascript:void(0)">FR</a>
        </nav>
        <ul class="nav">
            @if(Auth::check())
                <li class="nav-item dropdown dropdown-hover">
                    <a class="nav-link dropdown-toggle pr-0"
                       data-toggle="dropdown" href="#" role="button"
                       aria-haspopup="true" aria-expanded="false">{{$user->getAttribute('username')}}</a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="{{route_i18n('profile')}}" class="dropdown-item">{{trans('general.user_profile')}}</a>
                        <div class="dropdown-divider"></div>
                        <form id="logout_form" accept-charset="UTF-8" action="{{route('logout')}}" method="POST">
                            <fieldset>
                                <input type="hidden" value="{{csrf_token()}}" name="_token">
                            </fieldset>
                        </form>
                        <a onclick="document.querySelector('#logout_form').submit()" href="#" class="dropdown-item">Logout</a>
                    </div>
                </li>
            @else
                <li class="nav nav-item">
                    <a class="nav-link" href="{{route_i18n('login')}}">{{trans('ajax.general.login')}}</a>
                </li>
            @endif
        </ul>
    </div>
</div>
<div id="wrapper"></div>
<header>
    <div class="container">
        <a class="nav-link nav-icon ml-ni nav-toggler mr-3 d-flex d-lg-none" href="#" data-toggle="modal"
           data-target="#menuModal">
        </a>
        <a class="nav-link nav-logo" href=""><strong>App</strong></a>
        <ul class="nav nav-main d-none d-lg-flex">
            <li class="nav-item"><a class="nav-link active" href="">Home</a></li>

            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle forwardable" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="true" aria-expanded="false">Shop</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="shop-categ">Shop Categories</a>
                    <a class="dropdown-item" href="shop">Shop Grid</a>
                    <a class="dropdown-item" href="shop">Shop List</a>
                    <a class="dropdown-item" href="shop-s">Single Product</a>
                    <a class="dropdown-item" href="shop-si">Single Product v2</a>
                    <a class="dropdown-item" href=>Cart</a>
                    <a class="dropdown-item" href="shi">Checkout</a>
                </div>
            </li>

            <li class="nav-item dropdown dropdown-hover dropdown-mega">
                <a class="nav-link dropdown-toggle forwardable" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="true" aria-expanded="false">Mega</a>
                <div class="dropdown-menu">
                    <div class="row">
                        <div class="col-lg-3 border-right">
                            <div class="list-group list-group-flush list-group-no-border list-group-sm">
                                <a href="shop-grid.html"
                                   class="list-group-item list-group-item-action"><strong>Link1</strong></a>
                                <a href="shop-grid.html" class="list-group-item list-group-item-action">Link2
                                    Shoes</a>
                                <a href="shop-grid.html" class="list-group-item list-group-item-action">Link3</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>
<div id="app">
    <div id="content_container" class="container">
        @if(isset($breadcrumbs))
            <div id="breadcrumb-wrapper" class="col">
                    <div class="card">
                        {!! $breadcrumbs !!}
                    </div>
                </div>
            </div>
        @endif
        @yield('content')
    </div>
</div>

<!-- Scripts -->
@include('partials.javascript_footer')
<script src="{{ mix('js/app.js','6aa0e') }}"></script>
</body>
</html>
