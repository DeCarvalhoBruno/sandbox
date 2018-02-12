@extends('admin.layouts.default')
@section('content')
    <div id="app">
        <header class="main-header">
            <!-- Logo -->
            <div id="logo_wrapper">
                <a href="index2.html" class="logo">
                    <!-- mini logo for sidebar mini 50x50 pixels -->
                    <span class="logo-mini">LV</span>
                    <!-- logo for regular state and mobile devices -->
                    <span class="logo-lg">Laravel</span>
                </a>
            </div>

            <nav class="navbar navbar-expand-lg navbar-dark bg-dark main-header navbar-static-top">
                <button class="navbar-toggler" type="button" data-toggle="push-menu"
                        data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <span><a class="nav-link sidebar-toggle" data-toggle="push-menu" role="button"></a></span>
                        </li>
                        <li class="nav-item active">

                            <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                        </li>
                    </ul>
                    <div id="backend_actions_dropdown" class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            Dropdown
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="#">Something else here</a>
                            <a class="dropdown-item" href="{{route('admin.logout')}}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                Logout
                            </a>
                            <div class="dropdown-divider"></div>
                            <form id="logout-form" action="{{route('admin.logout')}}" method="POST"
                                  style="display: none;">
                                <input type="hidden" value="{!!csrf_token()!!}" name="_token">
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <b-drawer :menu-items="MenuItems"></b-drawer>
        <b-content-wrapper></b-content-wrapper>
        {{--<Modal></Modal>--}}
    </div>
@endsection
@include('layouts.footer')
@routes