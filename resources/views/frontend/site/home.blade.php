@extends('frontend.default')

@section('content')
    <section id="blog-featured">
        <div class="row">
            <div id="blog-featured-carousel" class="col-lg-7 col-md-12">
                <div id="carousel-home" class="carousel slide" data-ride="false">
                    <ol class="carousel-indicators">
                        <li data-target="#carousel-home" data-slide-to="0" class="active"></li>
                        <li data-target="#carousel-home" data-slide-to="1"></li>
                        <li data-target="#carousel-home" data-slide-to="2"></li>
                    </ol>
                    <div class="featured-content carousel-inner">
                        @for($i=0;$i<3;$i++)
                            <div class="carousel-item {{($i==0)?'active':''}}">
                                <div class="carousel-featured">
                                    <div class=carousel-featured-content">
                                        <a class="fc-cat badge-success"
                                           href="#">{{$posts['featured'][$i]['cat']}}</a>
                                        <h2 class="fc-title">
                                            <a href="#">{{$posts['featured'][$i]['title']}}</a>
                                        </h2>
                                        <span class="fc-date">{{$posts['featured'][$i]['date']}}</span>
                                    </div>
                                </div>
                                <figure>
                                    <img src="{{asset($posts['featured'][$i]['img'])}}"
                                         class="d-block" alt="...">
                                </figure>
                            </div>
                        @endfor
                    </div>
                    <a class="carousel-control-prev" href="#carousel-home" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">{{trans('ajax.general.prev')}}</span>
                    </a>
                    <a class="carousel-control-next" href="#carousel-home" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">{{trans('ajax.general.next')}}</span>
                    </a>
                </div>
            </div>
            <div id="blog-featured-right" class="col-lg-5 col-md-12">
                <div class="container p-0">
                    <div class="row">
                        <div class="col-lg-12 featured-content featured-content-top">
                            <div class="right-featured">
                                <div class=right-featured-content">
                                    <a class="fc-cat badge-success"
                                       href="#">{{$posts['featured'][3]['cat']}}</a>
                                    <h2 class="fc-title">
                                        <a href="#">{{$posts['featured'][3]['title']}}</a>
                                    </h2>
                                </div>
                            </div>
                            <figure>
                                <img src="{{asset($posts['featured'][3]['img'])}}">
                            </figure>
                        </div>

                        <div class="col-lg-6 col-md-12 featured-content featured-content-bottom">
                            <div class="bottom-featured">
                                <div class=bottom-featured-content">
                                    <a class="fc-cat badge-success"
                                       href="#">{{$posts['featured'][4]['cat']}}</a>
                                    <h2 class="fc-title">
                                        <a href="#">{{$posts['featured'][4]['title']}}</a>
                                    </h2>
                                </div>
                            </div>
                            <figure>
                                <figure>
                                    <img src="{{asset($posts['featured'][4]['img'])}}">
                                </figure>
                            </figure>
                        </div>
                        <div class="col-lg-6 col-md-12 featured-content">
                            <div class="bottom-featured">
                                <div class=bottom-featured-content">
                                    <a class="fc-cat badge-success"
                                       href="#">{{$posts['featured'][5]['cat']}}</a>
                                    <h2 class="fc-title">
                                        <a href="#">{{$posts['featured'][5]['title']}}</a>
                                    </h2>
                                </div>
                            </div>
                            <figure>
                                <figure>
                                    <img src="{{asset($posts['featured'][5]['img'])}}">
                                </figure>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="blog-spotlight" class="container p-0 m-0">
        <div class="row">
            <div class="col-lg col-lg-8 col-md-12 spotlight-container">
                <div class="container">
                    <ul class="row">
                        @foreach($posts['most_viewed_cat'] as $keyMostViewed =>$mostViewedItems)
                            <li class="col-lg col-lg-6 col-md-12 spotlight-category">
                                <h5><span>{{$keyMostViewed}}</span></h5>
                                <ul class="container">
                                    <li class="row headline-post">
                                        <div class="headline-content">
                                            <div class="lfc-title">{{$mostViewedItems[0]['title']}}</div>
                                            <div class="lfc-date">{{$mostViewedItems[0]['date']}}</div>
                                        </div>
                                        <figure>
                                            <img src="{{asset($mostViewedItems[0]['img'])}}">
                                        </figure>
                                    </li>
                                    @for($i=1;$i<=4;$i++)
                                        <li class="row list-post">
                                            <div class="container">
                                                <div class="row d-flex align-items-center">
                                                    <div class="col-lg-3 col-md-6 list-img-container">
                                                        <figure>
                                                            <img src="{{asset($mostViewedItems[$i]['img'])}}">
                                                        </figure>
                                                    </div>
                                                    <div class="col-lg-9 col-md-6 list-txt-container">
                                                        <div class="row lfc-title">{{$mostViewedItems[$i]['title']}}</div>
                                                        <div class="row lfc-date">{{$mostViewedItems[$i]['date']}}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endfor
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="container">
                    <div class="row">
                        <div class="col">
                            <h5><span>{{trans('titles.follow_us')}}</span></h5>
                            <div class="container p-0 text-center">
                                <div class="row">
                                    <ul class="col social-icon">
                                        <li><a href="#" target="_blank"><i class="fa fa-rss"></i></a></li>
                                        <li><a href="#" target="_blank"><i class="fa fa-facebook"></i></a></li>
                                        <li><a href="#" target="_blank"><i class="fa fa-twitter"></i></a></li>
                                        <li><a href="#" target="_blank"><i class="fa fa-google-plus"></i></a></li>
                                        <li><a href="#" target="_blank"><i class="fa fa-vimeo-square"></i></a></li>
                                        <li><a href="#" target="_blank"><i class="fa fa-youtube"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="blog-mvp-container" class="row">
                        <div class="col">
                            <h5><span>{{trans('titles.most_viewed')}}</span></h5>
                            <div class="container mvp-list">
                                @foreach($posts['most_viewed'] as $mostViewedItems)
                                    <div class="row mvp-list-item d-flex align-items-md-center align-items-lg-start">
                                        <div class="col-lg-3 col-md-6 list-img-container">
                                            <figure>
                                                <img src="{{asset($mostViewedItems['img'])}}">
                                            </figure>
                                        </div>
                                        <div class="col-lg-9 col-md-6 list-txt-container">
                                            <div class="row mli-date">{{$mostViewedItems['date']}}</div>
                                            <div class="row mli-title">{{$mostViewedItems['title']}}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
