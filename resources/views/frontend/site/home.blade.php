@extends('frontend.default')

@section('content')
    <div id="blog-featured" class="container">
        <div class="row">
            <div id="blog-featured-carousel" class="col-lg-7">
                <div id="carousel-1" class="carousel slide" data-ride="false">
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-1" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-1" data-slide-to="1"></li>
                        <li data-target="#carousel-1" data-slide-to="2"></li>
                    </ol>
                    <div class="carousel-inner">
                        <div class="carousel-item active" style="text-align: center;overflow:hidden">
                            <figure>
                                <img src="media/img/1.jpg"
                                     class="d-block" alt="...">
                            </figure>
                        </div>
                        <div class="carousel-item" style="text-align: center;overflow:hidden">
                            <figure>
                                <img src="media/img/2.jpg"
                                     class="d-block" alt="...">
                            </figure>
                        </div>
                        <div class="carousel-item" style="text-align: center;overflow:hidden">
                            <figure>
                                <img src="media/img/3.jpg"
                                     class="d-block" alt="...">
                            </figure>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#carousel-1" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carousel-1" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
            <div id="blog-featured-right" class="col-lg-5">
                <div class="row container">
                    <div class="col-lg-12">
                        <figure>
                            <img src="media/img/4.jpg">
                        </figure>
                    </div>
                    <div class="col-lg-6">
                        <figure>
                            <img src="media/img/5.jpg">
                        </figure>
                    </div>
                    <div class="col-lg-6">
                        <figure>
                            <img src="media/img/2.jpg">
                        </figure>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
