@extends('frontend.default')

@section('content')
    <div id="blog-post" class="container p-0">
        <div class="row post-title">
            <div class="post-title-label">
                <div class="title-label">
                <h3>{{$post->getAttribute('title')}}</h3>
                <span>{{$post->getAttribute('date')}}</span>
                </div>
            </div>
            <figure>
                <img src="{{asset($media['featured']->getAttribute('img'))}}">
            </figure>
        </div>
        <div class="row mt-2">
            <div class="col-lg-8 col-md-12">
                <div class="card border-0">
                    {!! $post->content!!}
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="card border-0">
                    dsfsfdfsdf
                </div>
            </div>
        </div>
    </div>
@endsection