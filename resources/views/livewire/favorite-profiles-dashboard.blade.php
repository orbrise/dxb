<div>
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
@endpush

<style>
    /* --------------------------------------------------------------
       Favorites — matches the listing/fallback-card style used across
       the site (home, category pages). Row layout: main image (200x210)
       + 65px thumbnail column + info panel on the right. Thin divider
       between rows. Filled lime heart overlay for unfavorite action.
       -------------------------------------------------------------- */

    /* Full-width sub-header bar — mirrors the "Back / <title>" strip
       used on other evoory account pages. Background spans edge-to-edge
       of the viewport, but the inner "Back" link + title sit inside the
       same .ev-container (max-width:1200px, 16px side padding) that the
       top header uses — so the "Back" button lines up vertically with
       the evoory logo above it. */
    .fav-subheader {
        background: #131616;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        padding: 14px 0;
        margin-bottom: 8px;
    }

    /* Force page body to pure black on this route so the subheader strip
       stands out and the rest of the shell matches the design. */
    body { background: #000 !important; }

    /* Favorites page only: strip the top-header's dark strip so the
       header blends into the pure-black body. Compound selector
       (header.ev-header) because the class sits directly on the
       <header> element in header-evoory.blade.php. */
    header.ev-header { background: transparent !important; }
    .fav-subheader-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        min-height: 24px;
    }
    .fav-subheader-back {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #C1F11D;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
    }
    .fav-subheader-back:hover { color: #d4ff3a; text-decoration: none; }
    .fav-subheader-title {
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }
    @media (max-width: 640px) {
        .fav-subheader { padding: 12px 0; }
        .fav-subheader-back { font-size: 13px; }
        .fav-subheader-back span { display: none; }
        .fav-subheader-title { font-size: 15px; }
    }

    .fav-shell {
        width: 70%;
        margin: 0 auto;
        padding: 24px 16px 48px;
    }
    @media (max-width: 900px) {
        .fav-shell { width: 100%; }
    }

    /* Page header strip — mirrors the "My Favorite Profiles" + subtitle
       from the reference design. */
    .fav-head {
        padding: 24px 4px 12px;
    }
    .fav-head h1 {
        margin: 0 0 6px;
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
    }
    .fav-head p {
        margin: 0;
        font-size: 14px;
        color: rgba(255,255,255,0.55);
    }

    /* Flash */
    .fav-alert {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 16px;
        margin-bottom: 16px;
        border-radius: 10px;
        background: rgba(193,241,29,0.1);
        border: 1px solid rgba(193,241,29,0.3);
        color: #C1F11D;
        font-size: 14px;
    }
    .fav-alert .fav-alert-close {
        background: none; border: none; color: inherit; cursor: pointer;
        font-size: 18px; line-height: 1; padding: 0 4px;
    }

    /* Row list — stacked flex rows with a thin separator between items.
       No grid; each row spans full width exactly like the main listing
       page's fallback-card layout. */
    .fav-list { margin-top: 8px; }
    .fav-row {
        position: relative;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: flex-start;
        gap: 12px;
        padding: 18px 48px 18px 0; /* right padding leaves room for heart */
        width:70%;
    }
    .fav-row + .fav-row {
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    /* Main image — 200x210, matches .ev-fallback-main from listings. */
    .fav-row-main {
        flex: 0 0 200px;
        width: 200px;
        max-width: 200px;
        height: 210px;
        display: block;
        border-radius: 6px;
        overflow: hidden;
        background: #111;
        line-height: 0;
    }
    .fav-row-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Thumbnail column — 3 stacked 65x65 thumbnails to the right of main. */
    .fav-row-thumbs {
        flex: 0 0 65px;
        width: 65px;
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    .fav-row-thumb {
        display: block;
        width: 65px;
        height: 65px;
        border-radius: 4px;
        overflow: hidden;
        background: #111;
        line-height: 0;
    }
    .fav-row-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Info panel — name heading + description paragraph + "See more
       contact" ghost button. */
    .fav-row-info {
        flex: 1 1 0;
        min-width: 0;
        padding-left: 8px;
        overflow: hidden;
    }
    .fav-row-name {
        margin: 0 0 10px;
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: calc(100% - 50px); /* leave room for heart */
    }
    .fav-row-name a {
        color: #fff;
        text-decoration: none;
    }
    .fav-row-name a:hover { color: #C1F11D; }
    .fav-row-desc {
        color: #c9c9c9;
        font-size: 14px;
        line-height: 1.5;
        margin: 0 0 14px;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        overflow: hidden;
        overflow-wrap: anywhere;
        word-break: break-word;
    }
    .fav-row-cta {
        display: inline-block;
        background:  #131616;
        color: #C1F11D;
        padding: 8px 18px;
        border-radius: 5px;
        font-size: 13px;
        text-decoration: none;
        transition: background 0.15s ease;
    }
    .fav-row-cta:hover {
        background: #2a2a2a;
        color: #C1F11D;
        text-decoration: none;
    }

    /* Heart — outlined lime circle anchored to the row's top-right.
       Transparent fill, 2px lime border, lime heart glyph inside. */
    .fav-row-heart {
        position: absolute;
        top: 18px;
        right: 0;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid #C1F11D;
        background: transparent;
        color: #C1F11D;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background 150ms, color 150ms, border-color 150ms, transform 150ms;
    }
    .fav-row-heart:hover {
        background: rgba(239, 68, 68, 0.12);
        border-color: #ef4444;
        color: #ef4444;
        transform: scale(1.06);
    }
    .fav-row-heart i { font-size: 14px; }

    /* Empty state. */
    .fav-empty {
        padding: 60px 24px;
        text-align: center;
        background: #0f1012;
        border: 1px dashed rgba(255,255,255,0.1);
        border-radius: 16px;
        margin-top: 20px;
    }
    .fav-empty-icon {
        width: 88px; height: 88px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: rgba(193,241,29,0.08);
        color: #C1F11D;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }
    .fav-empty h3 {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        margin: 0 0 8px;
    }
    .fav-empty p {
        color: rgba(255,255,255,0.55);
        font-size: 14px;
        line-height: 1.5;
        margin: 0 auto 20px;
        max-width: 420px;
    }
    .fav-empty-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: #C1F11D;
        color: #0a0a0a;
        font-weight: 700;
        font-size: 14px;
        padding: 10px 22px;
        border-radius: 999px;
        text-decoration: none;
    }
    .fav-empty-btn:hover {
        background: #d4ff3a;
        color: #0a0a0a;
        text-decoration: none;
    }

    /* --------------- Responsive ---------------------------------
       Mirror the home-page fallback-card mobile treatment: title above
       the image row, image + stacked thumbs as one connected unit,
       description + CTA below full-width. */
    @media (max-width: 640px) {
        .fav-head h1 { font-size: 22px; }
        .fav-head p { font-size: 13px; }

        .fav-row {
            gap: 0;
            flex-wrap: wrap;
            padding: 16px 0;
        }
        .fav-row-main {
            flex: 0 0 78%;
            width: 78%;
            max-width: none;
            height: auto;
            aspect-ratio: 1 / 1;
            border-radius: 10px 0 0 10px;
        }
        .fav-row-main img { border-radius: 0; }
        .fav-row-thumbs {
            flex: 0 0 22%;
            width: 22%;
            gap: 0;
        }
        .fav-row-thumb {
            width: 100%;
            max-width: none;
            height: calc((78vw - 32px) / 3);
            aspect-ratio: 1 / 1;
            border-radius: 0;
        }
        .fav-row-thumb img { border-radius: 0; }
        .fav-row-thumb:first-child { border-radius: 0 10px 0 0; }
        .fav-row-thumb:last-child  { border-radius: 0 0 10px 0; }
        .fav-row-info {
            flex: 0 0 100%;
            width: 100%;
            order: 99;
            padding-left: 0;
            margin-top: 12px;
        }
        .fav-row-name {
            white-space: normal;
            max-width: calc(100% - 50px);
            font-size: 18px;
        }
        .fav-row-cta {
            display: block;
            text-align: center;
            margin: 0 auto;
        }
        .fav-row-heart {
            top: 16px;
            right: 0;
            width: 32px; height: 32px;
        }
    }
</style>

<div class="fav-subheader">
    <div class="fav-subheader-inner">
        <a href="javascript:history.back()" class="fav-subheader-back" aria-label="Back">
            <i class="fa fa-angle-left"></i><span> Back</span>
        </a>
        <h2 class="fav-subheader-title">My Favorite Profiles</h2>
    </div>
</div>

<div class="fav-shell">
    @if(session('success'))
        <div class="fav-alert">
            <span><i class="fa fa-check-circle"></i> {{ session('success') }}</span>
            <button class="fav-alert-close" onclick="this.parentElement.style.display='none'" aria-label="Close">&times;</button>
        </div>
    @endif

    <div class="fav-head">
        <h1>My Favorite Profiles</h1>
        <p>Quick access to your shortlisted escort companions and luxury profiles.</p>
    </div>

    @if($favorites->total() > 0)
        <div class="fav-list">
            @foreach($favorites as $favorite)
                @php
                    $profile = $favorite->profile;
                    $profileUrl = url(strtolower($profile->ggender->name) . '-escorts-in-' . strtolower($profile->getcity->name) . '/' . $profile->id . '/' . $profile->slug);
                    if (!empty($profile->coverimg->image)) {
                        $mainImg = smart_asset('userimages/' . $profile->user_id . '/' . $profile->id . '/' . $profile->coverimg->image);
                    } elseif (!empty($profile->singleimg->image)) {
                        $mainImg = smart_asset('userimages/' . $profile->user_id . '/' . $profile->id . '/' . $profile->singleimg->image);
                    } else {
                        $mainImg = smart_asset('assets/images/default-avatar.png');
                    }
                    $thumbs = $profile->multipleimgs ? $profile->multipleimgs->take(3) : collect();
                @endphp
                <div class="fav-row" wire:key="fav-{{ $profile->id }}">
                    <a class="fav-row-main" href="{{ $profileUrl }}">
                        <img src="{{ $mainImg }}" alt="{{ $profile->name }}" loading="lazy">
                    </a>
                    @if($thumbs->isNotEmpty())
                        <div class="fav-row-thumbs">
                            @foreach($thumbs as $k => $imgs)
                                <a class="fav-row-thumb" href="{{ $profileUrl }}">
                                    <img src="{{ smart_asset('userimages/' . $imgs->user_id . '/' . $imgs->profile_id . '/' . $imgs->image) }}" alt="{{ $profile->name }} - Photo {{ $k + 1 }}" loading="lazy">
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="fav-row-info">
                        <h2 class="fav-row-name">
                            <a href="{{ $profileUrl }}">{{ $profile->name }}</a>
                        </h2>
                        @if(!empty($profile->about))
                            <p class="fav-row-desc">{{ \Str::limit(strip_tags($profile->about), 280) }}</p>
                        @endif
                        <a href="{{ $profileUrl }}" class="fav-row-cta">See more contact</a>
                    </div>
                    <button type="button"
                            class="fav-row-heart"
                            wire:click="removeFavorite({{ $profile->id }})"
                            onclick="event.stopPropagation()"
                            title="Remove from favorites"
                            aria-label="Remove from favorites">
                        <i class="fa fa-heart"></i>
                    </button>
                </div>
            @endforeach
        </div>

        @if($favorites->hasPages())
            <div style="margin-top: 24px;">
                {{ $favorites->links() }}
            </div>
        @endif
    @else
        <div class="fav-empty">
            <div class="fav-empty-icon"><i class="fa fa-heart"></i></div>
            <h3>No favorites yet</h3>
            <p>Browse profiles and tap the heart on any you want to come back to later — they'll show up here for quick access.</p>
            <a href="/female-escorts-in-dubai" class="fav-empty-btn">
                <i class="fa fa-search"></i> Browse profiles
            </a>
        </div>
    @endif
</div>
</div>
