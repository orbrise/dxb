<div style="display: inline-flex; vertical-align: middle; margin-top: 7px;">
    <button wire:click="toggleFavorite" class="favorite-bookmark-btn {{ $isFavorited ? 'bookmarked' : '' }}" type="button" title="{{ $isFavorited ? 'Remove bookmark' : 'Add bookmark' }}">
        <i class="fa fa-bookmark"></i>
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

        .favorite-bookmark-btn i {
            font-size: 12px;
            line-height: 1;
        }

        .favorite-bookmark-btn:hover {
            color: #C1F11D;
            border-color: rgba(193, 241, 29, .5);
            background: rgba(193, 241, 29, .08);
        }

        .favorite-bookmark-btn:focus {
            outline: none;
        }

        .favorite-bookmark-btn.bookmarked {
            color: #C1F11D;
            border-color: rgba(193, 241, 29, .5);
            background: rgba(193, 241, 29, .12);
        }

        .favorite-bookmark-btn.bookmarked i {
            animation: bookmarkPop 0.3s ease;
        }

        @keyframes bookmarkPop {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
    </style>
</div>
