@extends('partials.website.default')

@section('content')
    <div class="container">
        @if(is_null($status))
            <div class="row justify-content-md-center mt-5">
                @else
                    <div class="row justify-content-md-center mt-5">
                        <div class="col-md-8">
                            <div class="alert alert-success" role="alert">
                                <h4 class="alert-heading">{{ trans(sprintf('auth.alerts.%s_title', $status)) }}</h4>
                                <p>{{ trans(sprintf('auth.alerts.%s_body', $status)) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-md-center mt-1">
                        @endif
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">Login</div>
                                <div class="card-body">
                                    <form class="form-horizontal" method="POST" action="{{ route('login.post') }}">
                                        {{ csrf_field() }}

                                        <div class="form-group row">
                                            <label for="email" class="col-lg-4 col-form-label text-lg-right">E-Mail
                                                Address</label>

                                            <div class="col-lg-6">
                                                <input
                                                        id="email"
                                                        type="email"
                                                        class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                                        name="email"
                                                        value="{{ old('email') }}"
                                                        required
                                                        autofocus
                                                >

                                                @if ($errors->has('email'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label for="password"
                                                   class="col-lg-4 col-form-label text-lg-right">Password</label>

                                            <div class="col-lg-6">
                                                <input
                                                        id="password"
                                                        type="password"
                                                        class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                                        name="password"
                                                        required
                                                >

                                                @if ($errors->has('password'))
                                                    <div class="invalid-feedback">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-6 offset-lg-4">
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                        <input type="checkbox" class="form-check-input"
                                                               name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                        Remember Me
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-lg-8 offset-lg-4">
                                                <button type="submit" class="btn btn-primary">
                                                    Login
                                                </button>

                                                <a class="btn btn-link" href="{{ route_i18n('password.request') }}">
                                                    Forgot Your Password?
                                                </a>
                                            </div>
                                        </div>
                                        <div class="form-group row text-center m-0">
                                            <div class="col align-content-lg-center">

                                                <a class="btn btn-link" href="{{ route_i18n('register') }}">
                                                    Create an account
                                                </a>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
@endsection
