@extends('frontend.default')

@section('content')
    <div id="blog-post" class="container p-0">
        <div class="row post-title">
            <div class="post-title-label">
                <div class="title-label">
                    <h3>{{$post->getAttribute('title')}}</h3>
                    <div>
                        <span class="date-label"><i
                                    class="fa fa-clock-o"></i>{{new \Carbon\Carbon($post->getAttribute('date'))}}</span>
                        <span class="author-label"><i class="fa fa-user-circle-o"></i><a
                                    href="{{route_i18n('blog.author',$post->getAttribute('author'))}}">{{$post->getAttribute('person')}}</a></span>
                        <span class="page-views-label"><i title="{{trans('pages.blog.page_view_count')}}"
                                                          class="fa fa-eye"></i>{{$post->getAttribute('page_views')}}
                        </span>
                    </div>
                </div>
            </div>
            @include('partials.img',[
                'media'=>!is_null($media)?$media->asset(
                \App\Models\Entity::BLOG_POSTS,
                \App\Models\Media\Media::IMAGE,
                \App\Models\Media\MediaImgFormat::FEATURED
                ):null,
                'alt'=>$post->getAttribute('title')
            ])
        </div>
        <div class="row card post-content-wrapper">
            <div class="col">
                <div class="container">
                    <div class="row">
                        <div class="post-content">
                            {!! $post->content!!}
                        </div>
                    </div>
                    @if(!$sources->isEmpty())
                        <div id="post-source-wrapper" class="row">
                            <div class="col">
                                <ul class="post-sources">
                                    <li class="d-block">References:</li>
                                    @foreach($sources as $source)
                                        <li class="source-label">
                                                <i class="fa fa-link" title="{{trans('pages.blog.source_types.url')}}"></i>
                                            {{$source->getAttribute('description')}}
                                            <a href="{{$source->getAttribute('source')}}">{{$source->getAttribute('source')}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                    @if(!$tags->isEmpty())
                        <div id="post-tag-wrapper" class="row">
                            <div class="col">
                                <ul class="post-tags">
                                    <li class="d-inline-flex">Tags:</li>
                                    @foreach($tags as $tag)
                                        <li class="badge badge-info d-inline-flex"><a
                                                    href="{{route_i18n('blog.tag',$tag->getAttribute('tag'))}}">{{$tag->getAttribute('name')}}</a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div id="blog-post-other" class="row">
            @foreach($otherPosts as $otherPost)
                <div class="col-lg-3 col-md-6 col-sm-12 post-other-item">
                    <div class="card">
                        @if(isset($otherPostMedia[$otherPost->getAttribute('type')]))
                            <img src="{{$otherPostMedia[$otherPost->getAttribute('type')]->asset(
                                    \App\Models\Entity::BLOG_POSTS,
                                    \App\Models\Media\Media::IMAGE,
                                    \App\Models\Media\MediaImgFormat::FEATURED
                                    )}}" class="card-img-top" alt="{{$otherPost->getAttribute('title')}}">
                        @else
                            <img src="{{asset(sprintf('/media/img/site/placeholder_%s.png',\App\Models\Media\MediaImgFormat::getFormatAcronyms(\App\Models\Media\MediaImgFormat::FEATURED)))}}"
                                 alt="{{$otherPost->getAttribute('title')}}">
                        @endif
                        <div class="card-body">
                            <div class="label-more-reading">
                                {{trans('pages.blog.more_reading')}}
                            </div>
                            <div class="label-title">
                                <a href="{{route_i18n('blog',$otherPost->getAttribute('slug'))}}">{{$otherPost->getAttribute('title')}}</a>
                            </div>
                            <div class="label-rest">
                                <div class="label-date">
                                    <i class="fa fa-clock-o"></i>{{new \Carbon\Carbon($otherPost->getAttribute('date'))}}
                                </div>
                                <div class="author-label"><i class="fa fa-user-circle-o"></i><a
                                            href="{{route_i18n('blog.author',$otherPost->getAttribute('author'))}}">{{$otherPost->getAttribute('person')}}</a>
                                </div>
                                <div class="page-views-label"><i title="{{trans('pages.blog.page_view_count')}}"
                                                                 class="fa fa-eye"></i>{{$otherPost->getAttribute('page_views')}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </div>
@endsection