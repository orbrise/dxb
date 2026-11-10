{{-- Image + video count badges rendered as a pill cluster at the top-left
     of a listing card's main thumbnail. Expects `$profile` with
     `images_count` and `videos_count` properties attached by
     HomePage::attachThumbnailImages(). Picture-frame + play icons. --}}
@php
    $imgCount = (int) ($profile->images_count ?? 0);
    $vidCount = (int) ($profile->videos_count ?? 0);
@endphp
@if($imgCount > 0 || $vidCount > 0)
<span class="ev-media-badges" aria-hidden="true">
    @if($imgCount > 0)
    <span class="ev-media-badge ev-media-badge--photos" title="{{ $imgCount }} photos">
        {{-- Picture icon, based on assets.evoory.com/assets/images/photo_image_icon.svg
             but slightly landscape (13x11) with rx=5 corners; white swapped for
             currentColor so it follows the badge text colour. --}}
        <svg viewBox="0 0 70 60" width="13" height="11"><rect x="4" y="4" width="62" height="52" rx="5" fill="none" stroke="currentColor" stroke-width="5"/><circle cx="47" cy="20" r="5.5" fill="currentColor"/><path d="M8.5 48 L24.5 26 L37.5 40 L45.5 33 L61.5 49 L61.5 52 L8.5 52 Z" fill="currentColor"/></svg>
        {{ $imgCount }}
    </span>
    @endif
    @if($vidCount > 0)
    <span class="ev-media-badge ev-media-badge--video" title="{{ $vidCount }} video{{ $vidCount > 1 ? 's' : '' }}">
        {{-- Play-circle: white circle with the play triangle cut out. --}}
        <svg viewBox="0 0 24 24" width="13" height="13" fill="currentColor"><path fill-rule="evenodd" d="M12 2a10 10 0 1 1 0 20 10 10 0 0 1 0-20zM10 8v8l6.2-4z"/></svg>
        Video
    </span>
    @endif
</span>
@endif
