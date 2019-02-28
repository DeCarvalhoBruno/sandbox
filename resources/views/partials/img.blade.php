@if(!is_null($media))
    <figure>
        @if(isset($alt))
            <img class="lazy" src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="{{asset($media)}}" alt="{{$alt}}"/>
        @else
            <img src="{{asset($media)}}"/>
        @endif
    </figure>
@else
    <figure>
        @if(isset($format))
            @if(isset($alt))
                <img class="lazy" src="data:image/png;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="{{asset(sprintf('/media/img/site/placeholder_%s.png',\App\Models\Media\MediaImgFormat::getFormatAcronyms($format)))}}"
                     alt="{{isset($alt)??''}}"/>
            @else
                <img src="{{asset(sprintf('/media/img/site/placeholder_%s.png',\App\Models\Media\MediaImgFormat::getFormatAcronyms($format)))}}"/>
            @endif
        @else
            @if(isset($alt))
                <img src="{{asset(sprintf('/media/img/site/placeholder_%s.png',\App\Models\Media\MediaImgFormat::getFormatAcronyms(\App\Models\Media\MediaImgFormat::FEATURED)))}}"
                     alt="{{isset($alt)??''}}"/>
            @else
                <img src="{{asset(sprintf('/media/img/site/placeholder_%s.png',\App\Models\Media\MediaImgFormat::getFormatAcronyms(\App\Models\Media\MediaImgFormat::FEATURED)))}}"/>
            @endif
        @endif
    </figure>
@endif