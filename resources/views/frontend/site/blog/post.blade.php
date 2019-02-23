@extends('frontend.default')

@section('content')
    <div id="blog-post" class="container p-0">
        <div class="row post-title">
            <div class="post-title-label">
                <div class="title-label">
                    <h3>{{$post->getAttribute('title')}}</h3>
                    <div>
                        <span class="date-label"><i class="fa fa-clock-o"></i>{{$post->getAttribute('date')}}</span>
                        <span class="page-views-label"><i title="{{trans('pages.blog.page_view_count')}}" class="fa fa-eye"></i>{{$post->getAttribute('page_views')}}</span>
                    </div>
                </div>
            </div>
            <figure>
                <img src="{{asset($media['featured']->getAttribute('img'))}}">
            </figure>
        </div>
        <div class="row card post-content-wrapper">
            <div class="col">
                <div class="post-content">
                    {!! $post->content!!}
                </div>
            </div>
        </div>
    </div>
@endsection