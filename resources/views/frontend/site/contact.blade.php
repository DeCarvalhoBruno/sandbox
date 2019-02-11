@extends('frontend.default')

@section('content')
    <div id="user-profile" class="container mt-4">
        <div class="col-md-10 offset-md-1">
            <div id="user_register_container" class="card card-shadow">
                <div class="card-body">
                    <inline-form :id="'register-form'" :action="'{{ route('contact.send') }}'"
                                 :method="'POST'">
                        {!! csrf_field() !!}
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">{{trans('pages.contact.sender_email')}}</label>
                            <div class="col-lg-6">
                                <input type="email"
                                       class="form-control"
                                       name="sender_email"
                                       maxlength="125"
                                       autocomplete="email"
                                       required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">{{trans('pages.contact.email_subject')}}</label>
                            <div class="col-lg-6">
                                <input type="text"
                                       class="form-control"
                                       name="email_subject"
                                       maxlength="125"
                                       autocomplete="given-name"
                                       required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label text-lg-right">{{trans('pages.contact.email_body')}}</label>
                            <div class="col-lg-6">
                                <textarea
                                        class="form-control"
                                        name="email_body"
                                        rows="3"
                                        required></textarea>
                            </div>
                        </div>
                        <div class="form-group row pt-3">
                            <div class="col-xl-8 offset-xl-2 col-lg-6 offset-lg-3">
                                <submit-button
                                        ref="submitButton"
                                        :block="true">{{trans('general.send')}}</submit-button>
                            </div>
                        </div>
                        <input type="hidden" class="g-recaptcha" name="g-recaptcha" value="">
                    </inline-form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('frontend.scripts.google-recaptcha',['action'=>'contact'])
@endsection
