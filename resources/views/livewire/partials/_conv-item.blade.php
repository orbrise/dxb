{{-- Single conversation row used by the Support / Favourites / Recent
     sections in the sidebar. Kept in a partial so we don't duplicate the
     40-line markup three times.

     Alpine state lives at the row level so the per-row kebab (desktop)
     and the long-press bottom sheet (mobile) share one `open` flag. --}}
<div
    class="conversation-item {{ $conversation['unread_count'] > 0 ? 'unread' : '' }} {{ !empty($conversation['is_pinned']) ? 'pinned' : '' }} {{ !empty($conversation['missed_calls']) ? 'has-missed' : '' }} {{ !empty($conversation['is_support']) ? 'is-support' : '' }}"
    :class="selConv == {{ $conversation['id'] }} ? 'active' : ''"
    wire:key="conv-{{ $conversation['id'] }}"
    x-data="@include('livewire.partials._row-longpress-xdata')"
    @pointerdown="longPressStart($event)"
    @pointerup="longPressCancel()"
    @pointercancel="longPressCancel()"
    @pointerleave="longPressCancel()"
    @pointermove="trackMove($event)"
    @contextmenu.prevent
    wire:click="selectConversation({{ $conversation['id'] }})"
    {{-- Optimistic Alpine update — flips the row into "active" state and
         hides the sidebar on mobile the instant you tap, before the
         Livewire round-trip returns. Prevents the 7–8s "frozen UI" feel
         on prod. The skeleton overlay in chat-main handles the message
         area until the server returns. --}}
    @click="selConv = {{ $conversation['id'] }}; selConvMeta = {{ \Illuminate\Support\Js::from([
        'id'        => $conversation['id'],
        'name'      => $conversation['other_user_name'] ?? 'Unknown',
        'email'     => $conversation['other_user_email'] ?? '',
        'avatarUrl' => $conversation['other_user_avatar_url'] ?? null,
        'isSupport' => !empty($conversation['is_support']),
    ]) }}"
>
    {{-- Avatar wrapper. The green presence dot (span.presence-dot-overlay)
         sits outside the circular .conversation-avatar so it can bleed over
         the avatar's rounded corner without being clipped by the avatar's
         overflow:hidden. It's the only presence signal in the list (the
         "Online / Offline" text chip beside the name was removed). --}}
    <div class="conversation-avatar-wrap">
        <div class="conversation-avatar">
            @if(!empty($conversation['is_support']))
                <i class="fa fa-headset" aria-hidden="true"></i>
            @elseif(!empty($conversation['other_user_avatar_url']))
                <img src="{{ $conversation['other_user_avatar_url'] }}" alt="" onerror="this.style.display='none'">
            @else
                {{ strtoupper(substr($conversation['other_user_name'] ?? '?', 0, 1)) }}
            @endif
        </div>
        @if(empty($conversation['is_support']) && !empty($conversation['is_online']))
            <span class="presence-dot-overlay" aria-label="Online" title="Online"></span>
        @endif
    </div>
    <div class="conversation-info">
        <div class="conversation-name">
            {{ $conversation['other_user_name'] ?? 'Unknown' }}
            @if(!empty($conversation['is_support']))
                {{-- WhatsApp-style blue verified tick: signals "official account"
                     at a glance so users trust the support row. --}}
                @include('livewire.partials._support-verified', ['size' => 16])
            @endif
        </div>
        <div class="conversation-preview">
            @if(!empty($conversation['is_support']))
                {{-- Always show the official-account tagline for the support
                     row, never the last message. Treats the sidebar entry
                     like a brand/channel header (WhatsApp's "WhatsApp Official"
                     row works the same way) rather than a chat preview. --}}
                Official Evoory Support Account
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
        {{-- Mobile long-press backdrop. Only visible when open; closes the
             sheet on tap. Rendered before the menu so the menu stacks on top. --}}
        <div class="conv-lp-backdrop"
             x-show="open"
             x-transition.opacity.duration.150ms
             @click.stop="open = false"
             x-cloak></div>

        {{-- Per-row kebab trigger — desktop-only; mobile uses long-press.
             click.stop so tapping the ⋮ doesn't open the conversation. --}}
        <div class="conv-kebab" @click.outside="open = false">
            <button type="button" class="conv-kebab-btn"
                    @click.stop="open = !open" aria-label="More">
                <i class="fa fa-ellipsis-v"></i>
            </button>
            {{-- Same markup serves desktop dropdown AND mobile bottom sheet;
                 CSS media queries re-skin position/size at <= 900px. --}}
            <div class="conv-kebab-menu"
                 x-show="open"
                 x-transition.opacity.duration.100ms
                 @click.stop
                 x-cloak>
                <div class="conv-lp-handle" aria-hidden="true"></div>
                <div class="conv-lp-title">{{ $conversation['other_user_name'] ?? 'Options' }}</div>
                <button type="button"
                        wire:click="togglePin({{ $conversation['id'] }})"
                        @click="open = false">
                    <i class="fa fa-star"></i>
                    {{ !empty($conversation['is_pinned']) ? 'Remove from Favorites' : 'Add to Favorites' }}
                </button>
                <button type="button"
                        wire:click="toggleArchive({{ $conversation['id'] }})"
                        @click="open = false">
                    <i class="fa {{ !empty($conversation['is_archived']) ? 'fa-box-open' : 'fa-archive' }}"></i>
                    {{ !empty($conversation['is_archived']) ? 'Unarchive chat' : 'Archive chat' }}
                </button>
                @if(!empty($conversation['other_user_id']))
                    <button type="button"
                            wire:click="toggleBlock({{ $conversation['other_user_id'] }})"
                            @click="open = false">
                        <i class="fa fa-ban"></i>
                        {{ !empty($conversation['is_blocked_by_me']) ? 'Unblock' : 'Block' }}
                    </button>
                @endif
                {{-- wire:confirm, not onclick="return confirm()": Livewire's
                     wire:click ignores preventDefault, so Cancel on the old
                     native confirm still deleted the chat. --}}
                <button type="button" class="danger"
                        wire:click="deleteConversation({{ $conversation['id'] }})"
                        wire:confirm="Delete this conversation?"
                        @click="open = false">
                    <i class="fa fa-trash"></i>
                    Delete chat
                </button>
            </div>
        </div>
    @endif
</div>
