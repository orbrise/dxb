<div style="display: inline-flex; vertical-align: middle; margin-top: 7px;">
    <button wire:click="toggleFavorite" class="favorite-bookmark-btn {{ $isFavorited ? 'bookmarked' : '' }}" type="button" title="{{ $isFavorited ? 'Remove bookmark' : 'Add bookmark' }}">
        {{-- Inline SVG, not Font Awesome. The `fa fa-bookmark` glyph was
             intermittently invisible on wire:navigate (visible empty circle
             with no icon inside) because the FA font file hadn't finished
             loading when the Livewire component painted. Every other icon
             on the profile page is already inline SVG for this reason. --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="currentColor" stroke="none" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
        <span class="sr-only">{{ $isFavorited ? 'Remove bookmark' : 'Add bookmark' }}</span>
    </button>

    <style>
        /* Perfectly circular bookmark button. Width/height + flex centering keep
           the icon dead-center regardless of font metrics. The wrapper above gets
           the same margin-top as the sibling h1 so the button aligns with the
           title baseline instead of riding above it. */
        .favorite-bookmark-btn {
            width: 26px;
            height: 26px;
            padding: 0;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 50%;
            color: rgba(255, 255, 255, .75);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            margin-right: 6px;
            transition: color .2s ease, border-color .2s ease, background-color .2s ease;
        }

        .favorite-bookmark-btn svg {
            display: block;
            width: 12px;
            height: 12px;
        }

        .favorite-bookmark-btn:hover {
            color: #C1F11D;
            border-color: rgba(193, 241, 29, .5);
            background: rgba(193, 241, 29, .08);
        }

        .favorite-bookmark-btn:focus {
            outline: none;
        }

        /* Bookmarked = solid lime fill with a dark icon. Prior version
           used a 12% tinted background + lime-on-transparent icon, which
           read fine on the desktop button but was too subtle on the small
           mobile-header icon — the user couldn't tell "favorited or not".
           Solid fill is unambiguous at any size. */
        .favorite-bookmark-btn.bookmarked {
            color: #000;
            border-color: #C1F11D;
            background: #C1F11D;
        }

        .favorite-bookmark-btn.bookmarked:hover {
            color: #000;
            border-color: #d4f84d;
            background: #d4f84d;
        }

        .favorite-bookmark-btn.bookmarked svg {
            animation: bookmarkPop 0.3s ease;
        }

        @keyframes bookmarkPop {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</div>
