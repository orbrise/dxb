@if($type === 'css')
    @if($critical)
        <style>
            {!! file_get_contents(public_path($src)) !!}
        </style>
    @elseif($preload)
        <link rel="preload" href="{{ $getVersionedUrl() }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{{ $getVersionedUrl() }}"></noscript>
    @else
        <link rel="stylesheet" href="{{ $getVersionedUrl() }}">
    @endif

@elseif($type === 'js')
    @if($preload)
        <link rel="preload" href="{{ $getVersionedUrl() }}" as="script">
    @endif
    
    <script src="{{ $getVersionedUrl() }}" 
        @if($async) async @endif
        @if($defer) defer @endif
    ></script>

@elseif($type === 'icon')
    <link rel="icon" type="image/x-icon" href="{{ $getVersionedUrl() }}">

@elseif($type === 'preconnect')
    <link rel="preconnect" href="{{ $getAssetUrl() }}">
    <link rel="dns-prefetch" href="{{ $getAssetUrl() }}">

@endif
