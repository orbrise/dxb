{{-- Single conversation row used by the Support / Favourites / Recent
     sections in the sidebar. Kept in a partial so we don't duplicate the
     40-line markup three times. --}}
<div
    class="conversation-item {{ $selectedConversationId == $conversation['id'] ? 'active' : '' }} {{ $conversation['unread_count'] > 0 ? 'unread' : '' }} {{ !empty($conversation['is_pinned']) ? 'pinned' : '' }} {{ !empty($conversation['missed_calls']) ? 'has-missed' : '' }}"
    wire:click="selectConversation({{ $conversation['id'] }})"
    {{-- Optimistic Alpine update — flips the sidebar into "conversation
         open" mode client-side the instant you tap, so the transition
         feels instant on production where the Livewire round-trip is
         noticeable. Server response then fills in the actual thread. --}}
    @click="selConv = {{ $conversation['id'] }}"
    wire:key="conv-{{ $conversation['id'] }}"
>
    <div class="conversation-avatar {{ !empty($conversation['has_status']) ? 'status-ring' : '' }} {{ !empty($conversation['has_status']) && empty($conversation['status_unseen']) ? 'seen' : '' }}">
        @if(!empty($conversation['is_support']))
            <i class="fa fa-headset" aria-hidden="true"></i>
        @elseif(!empty($conversation['other_user_avatar_url']))
            <img src="{{ $conversation['other_user_avatar_url'] }}" alt="" onerror="this.style.display='none'">
        @else
            {{ strtoupper(substr($conversation['other_user_name'] ?? '?', 0, 1)) }}
        @endif
    </div>
    <div class="conversation-info">
        <div class="conversation-name">
            {{ $conversation['other_user_name'] ?? 'Unknown' }}
            @if(!empty($conversation['is_support']))
                <span class="support-badge">SUPPORT</span>
            @endif
        </div>
        <div class="conversation-preview">
            @if(empty($conversation['last_message']) && !empty($conversation['is_support']))
                Send us a message anytime
            @else
                {{ Str::limit($conversation['last_message'], 30) ?: 'No messages yet' }}
            @endif
        </div>
        @if(!empty($conversation['missed_calls']))
            <div class="missed-call-badge">
                <i class="fa fa-phone-slash"></i>
                {{ $conversation['missed_calls'] }} missed {{ $conversation['missed_calls'] === 1 ? 'call' : 'calls' }}
            </div>
        @endif
    </div>
    <div class="conversation-meta">
        @if($conversation['last_message_at'])
            <div class="conversation-time">
                {{ \Carbon\Carbon::parse($conversation['last_message_at'])->diffForHumans(null, true) }}
            </div>
        @endif
        @if($conversation['unread_count'] > 0)
            <span class="unread-badge">{{ $conversation['unread_count'] }}</span>
        @endif
    </div>

    @if(empty($conversation['is_support']))
        {{-- Per-row kebab menu: Mark favorite / Archive / Delete. Alpine-scoped
             so each row owns its open state. click.stop on the trigger stops
             the parent conversation-item click (which would open the thread). --}}
        <div class="conv-kebab" x-data="{ open: false }" @click.outside="open = false" @click.stop>
            <button type="button" class="conv-kebab-btn" @click="open = !open" aria-label="More">
                <i class="fa fa-ellipsis-v"></i>
            </button>
            <div class="conv-kebab-menu" x-show="open" x-transition.opacity.duration.100ms x-cloak>
                <button type="button"
                        wire:click="togglePin({{ $conversation['id'] }})"
                        @click="open = false">
                    <i class="fa fa-star"></i>
                    {{ !empty($conversation['is_pinned']) ? 'Unmark favorite' : 'Mark as favorite' }}
                </button>
                <button type="button"
                        wire:click="toggleArchive({{ $conversation['id'] }})"
                        @click="open = false">
                    <i class="fa {{ !empty($conversation['is_archived']) ? 'fa-box-open' : 'fa-archive' }}"></i>
                    {{ !empty($conversation['is_archived']) ? 'Unarchive' : 'Archive' }}
                </button>
                <button type="button" class="danger"
                        wire:click="deleteConversation({{ $conversation['id'] }})"
                        @click="open = false"
                        onclick="return confirm('Delete this conversation?')">
                    <i class="fa fa-trash"></i>
                    Delete
                </button>
            </div>
        </div>
    @endif
</div>
