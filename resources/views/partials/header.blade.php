<div class="topbar">
    <div class="container d-flex">
        <nav class="nav d-none d-md-flex"> <!-- hidden on xs -->
            <a class="nav-link" href="{{route_i18n('contact')}}">{{env('APP_EMAIL')}}</a>
        </nav>
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
        <a class="nav-link nav-logo" href="{{route_i18n('home')}}"><img src="{{asset('media/img/site/logo.png')}}"></a>
        <ul class="nav nav-main d-none d-lg-flex">
            <li class="nav-item"><a class="nav-link active" href="{{route_i18n('home')}}">{{trans('ajax.general.home')}}</a></li>
            <li class="nav-item dropdown dropdown-hover">
                <a class="nav-link dropdown-toggle forwardable" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="true" aria-expanded="false">Shop</a>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#">Shop Categories</a>
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
                                <a href="shop-grid.html" class="list-group-item list-group-item-action">Link2</a>
                                <a href="shop-grid.html" class="list-group-item list-group-item-action">Link3</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>