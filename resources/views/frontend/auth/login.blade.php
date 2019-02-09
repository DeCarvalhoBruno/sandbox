@extends('frontend.default-bare')
@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <img src="{{asset('media/img/site/logo.png')}}">
        </div>


        @if(is_null($status))
            <div class="row justify-content-md-center mt-5">
                <h3 class="font-light mb-0">{{trans('auth.login_account')}}</h3>
            </div>
            <div id="form-login" class="row justify-content-md-center mt-3">
                @else
                    <div class="row justify-content-md-center mt-3">
                        <div class="col-md-8">
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">{{ trans(sprintf('auth.alerts.%s_title', $status)) }}</h4>
                                <p>{{ trans(sprintf('auth.alerts.%s_body', $status)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-md-center">
                        @endif
                        <div class="col-md-8">
                            <div class="card card-shadow">
                                <div class="card-body">
                                    <form class="form-horizontal mt-3" method="POST" action="{{ route('login.post') }}">
                                        {{ csrf_field() }}
                                        <div class="form-group row">
                                            <label for="email"
                                                   class="col-md-4 col-form-label text-lg-right">E-Mail Address</label>
                                            <div class="col-md-6">
                                                <input id="email"
                                                       type="email"
                                                       class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                       name="email"
                                                       value="{{ old('email') }}"
                                                       autocomplete="username"
                                                       required
                                                       autofocus>
                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-md-4 col-form-label text-lg-right">{{trans('ajax.general.password')}}</label>
                                            <div class="col-md-6">
                                                <input id="password"
                                                       type="password"
                                                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                       name="password"
                                                       autocomplete="current-password"
                                                       required>
                                                @if ($errors->has('password'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-6 col-md-8 offset-md-2 offset-lg-3 d-flex justify-content-between">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox"
                                                           class="custom-control-input" id="customCheck1"
                                                           name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                           for="customCheck1">{{trans('ajax.pages.auth.remember_me')}}</label>
                                                </div>
                                                <u>
                                                    <a class="small"
                                                       href="{{ route_i18n('password.request') }}">
                                                        {{trans('ajax.pages.auth.forgot_password')}}
                                                    </a>
                                                </u>

                                            </div>
                                        </div>
                                        <div class="form-group row mt-5">
                                            <div class="col-xl-8 offset-xl-2 col-lg-6 offset-lg-3">
                                                <button type="submit" class="btn btn-primary btn-block">
                                                    {{trans('ajax.general.login')}}
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group row text-center m-0">
                                            <div class="col align-content-lg-center">
                                                <u>
                                                    <a class="small" href="{{ route_i18n('register') }}">
                                                        {{trans('auth.create_account')}}
                                                    </a>
                                                </u>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
    </div>
@endsection
