{{-- Image + video count badges rendered as a pill cluster at the top-left
     of a listing card's main thumbnail. Expects `$profile` with
     `images_count` and `videos_count` properties attached by
     HomePage::attachThumbnailImages(). Camera + play icons mirror the
     reference used by sites like mdir.cc / massagerepublic. --}}
@php
    $imgCount = (int) ($profile->images_count ?? 0);
    $vidCount = (int) ($profile->videos_count ?? 0);
@endphp
@if($imgCount > 0 || $vidCount > 0)
<span class="ev-media-badges" aria-hidden="true">
    @if($imgCount > 0)
    <span class="ev-media-badge ev-media-badge--photos" title="{{ $imgCount }} photos">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M20 5h-3.17L15 3H9L7.17 5H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm-8 12a5 5 0 1 1 0-10 5 5 0 0 1 0 10z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ $imgCount }}
    </span>
    @endif
    @if($vidCount > 0)
    <span class="ev-media-badge ev-media-badge--video" title="{{ $vidCount }} video{{ $vidCount > 1 ? 's' : '' }}">
        <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
        Video
    </span>
    @endif
</span>
@endif
