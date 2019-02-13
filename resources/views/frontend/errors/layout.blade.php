@extends('frontend.default')

@section('content')
    <div class="container">
        <div class="row error-page-wrapper">
            <div class="col align-self-center">
                <div class="card card-shadow">
                    <div class="container">
                        <div class="row">
                            <div class="col-xl-4 offset-xl-4 fa-container">
                                <span class="fa-stack-1x">
                                    <fa icon="circle"
                                        class="fa fa-stack-1x circle"></fa>
                                    <fa icon="exclamation"
                                        class="fa fa-stack-1x fa-inverse exclamation flash"></fa>
                                </span>
                            </div>
                        </div>
                        <div class="row code">
                            <div class="col text-center ">
                                <span>@yield('code')</span>
                            </div>
                        </div>
                        <div class="row message">
                            <div class="col text-center">
                                <span>@yield('message')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
