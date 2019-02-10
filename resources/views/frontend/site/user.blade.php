@extends('frontend.default')

@section('content')
    <section id="user-profile" class="container mt-4">
        <div class="col-md-8 offset-md-2">
            <div id="user_register_container" class="card card-shadow">
                <div class="card-body">
                    <inline-form :id="'register-form'" :action="'{{ route('profile.update') }}'"
                                 :method="'POST'">
                        {!! csrf_field() !!}
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">{{trans('ajax.db.first_name')}}</label>
                            <div class="col-lg-6">
                                <input type="text"
                                       class="form-control{{ $errors->has('first_name') ? ' is-invalid' : '' }}"
                                       name="first_name"
                                       value="{{ old('first_name',$user->getAttribute('first_name')) }}"
                                       maxlength="75"
                                       autocomplete="given-name">
                                @if ($errors->has('first_name'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('first_name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">{{trans('ajax.db.last_name')}}</label>
                            <div class="col-lg-6">
                                <input type="text"
                                       class="form-control{{ $errors->has('last_name',$user->getAttribute('last_name')) ? ' is-invalid' : '' }}"
                                       name="last_name"
                                       value="{{ old('last_name',$user->getAttribute('last_name')) }}"
                                       maxlength="75"
                                       autocomplete="family-name">
                                @if ($errors->has('last_name'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('last_name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">
                                <span class="form-has-help"
                                      data-toggle="tooltip"
                                      data-placement="top"
                                      data-original-title="{{trans('auth.register_username_help')}}">{{trans('pages.profile.new_username')}}</span>
                            </label>
                            <div class="col-lg-6">
                                <input type="text"
                                       class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}"
                                       name="username"
                                       value="{{ old('username') }}"
                                       maxlength="15"
                                       autocomplete="username">
                                @if ($errors->has('username'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </div>
                                @else
                                    <small id="emailHelp"
                                           class="form-text text-muted">{{trans('pages.profile.username_help',['username'=>$user->getAttribute('username')])}}</small>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">{{trans('pages.profile.new_email_address')}}</label>
                            <div class="col-lg-6">
                                <input type="email"
                                       class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                       name="email"
                                       value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </div>
                                @else
                                    <small id="emailHelp"
                                           class="form-text text-muted">{{trans('pages.profile.email_help',['email'=>$user->getAttribute('email')])}}</small>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">
                                <span class="form-has-help"
                                      data-toggle="tooltip"
                                      data-placement="top"
                                      data-original-title="{{trans('auth.password_help')}}">{{trans('pages.profile.new_password')}}</span>
                            </label>
                            <div class="col-lg-6">
                                <password-strength
                                        :has-errors="{{ $errors->has('password') ? 'true' : 'false' }}"
                                        :name="'password'"
                                        :label-hide="'{{trans('auth.hide_password')}}'"
                                        :label-show="'{{trans('auth.show_password')}}'"
                                        :secure-length="6"
                                        :required="false">
                                </password-strength>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label
                                    class="col-lg-4 col-form-label text-lg-right">{{ trans('pages.profile.new_password_confirm') }}</label>
                            <div class="col-lg-6">
                                <input type="password"
                                       class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                       name="password_confirmation">
                                @if ($errors->has('password'))
                                    <div class="invalid-feedback">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="form-group row pt-3">
                            <div class="col-xl-8 offset-xl-2 col-lg-6 offset-lg-3">
                                <submit-button
                                        ref="submitButton"
                                        :block="true">{{trans('ajax.general.register')}}</submit-button>
                            </div>
                        </div>
                        <input type="hidden" id="g-recaptcha" name="g-recaptcha" value="">
                    </inline-form>
                </div>
            </div>
        </div>
    </section>
@endsection
