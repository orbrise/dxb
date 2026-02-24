<div style="display: inline-block;">
    <button wire:click="toggleFavorite" class="favorite-bookmark-btn {{ $isFavorited ? 'bookmarked' : '' }}" type="button" title="{{ $isFavorited ? 'Remove bookmark' : 'Add bookmark' }}">
        <i class="fa fa-bookmark fa-fw fa-lg"></i>
        <span class="sr-only">{{ $isFavorited ? 'Remove bookmark' : 'Add bookmark' }}</span>
    </button>

    <style>
        .favorite-bookmark-btn {
            background: transparent;
            border-radius: 50%;
            border: 1px solid rgba(255, 255, 255, .1);
            padding: 6px 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            line-height: 1;
            vertical-align: middle !important;
            display: inline-block;

        }
        
        
        .favorite-bookmark-btn:hover {
            color: rgba(244, 184, 39, .8) !important;
        }
        
        .favorite-bookmark-btn:focus {
            outline: none;
        }
        
        .favorite-bookmark-btn.bookmarked {
            color: #f4b827;
            border-color: rgba(244, 184, 39, .3);
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
