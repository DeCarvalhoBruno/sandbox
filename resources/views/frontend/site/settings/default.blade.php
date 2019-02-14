@extends('frontend.default')

@section('content')
    <div class="row mt-2">
        <div class="col-md-3" style="font-size: 0.9rem;">
            <div class="card">
                <div class="card-header">
                    Personal Settings
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action active" href="{{route_i18n('profile')}}">Profile</a></li>
                    <a class="list-group-item list-group-item-action" href="{{route_i18n('notifications')}}">Notifications</a></li>
                </div>
            </div>
        </div>
        <div class="col-md-9 pl-0">
            @yield('pane')
        </div>
    </div>
@endsection