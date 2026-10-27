{{-- Single conversation row used by the Support / Favourites / Recent
     sections in the sidebar. Kept in a partial so we don't duplicate the
     40-line markup three times.

     Alpine state lives at the row level so the per-row kebab (desktop)
     and the long-press bottom sheet (mobile) share one `open` flag. --}}
<div
    class="conversation-item {{ $conversation['unread_count'] > 0 ? 'unread' : '' }} {{ !empty($conversation['is_pinned']) ? 'pinned' : '' }} {{ !empty($conversation['missed_calls']) ? 'has-missed' : '' }}"
    :class="selConv == {{ $conversation['id'] }} ? 'active' : ''"
    wire:key="conv-{{ $conversation['id'] }}"
    x-data="{
        open: false,
        pressTimer: null,
        startX: 0, startY: 0,
        moved: false,
        pressed: false,
        longPressStart(e) {
            // Touch-only — desktop uses the kebab button, right-click is left alone.
            if (e.pointerType !== 'touch') return;
            this.moved = false;
            this.pressed = true;
            this.startX = e.clientX;
            this.startY = e.clientY;
            const row = e.currentTarget;
            clearTimeout(this.pressTimer);
            this.pressTimer = setTimeout(() => {
                if (this.moved || !this.pressed) return;
                this.open = true;
                if (navigator.vibrate) { try { navigator.vibrate(25); } catch(_){} }
                // Swallow the click that fires on finger-up after a long-press
                // so the row doesn't open the conversation under the menu.
                const stopClick = (ev) => {
                    ev.preventDefault();
                    ev.stopImmediatePropagation();
                };
                row.addEventListener('click', stopClick, { capture: true, once: true });
                setTimeout(() => row.removeEventListener('click', stopClick, true), 700);
            }, 500);
        },
        longPressCancel() {
            this.pressed = false;
            clearTimeout(this.pressTimer);
            this.pressTimer = null;
        },
        trackMove(e) {
            if (!this.pressed) return;
            if (Math.abs(e.clientX - this.startX) > 10 || Math.abs(e.clientY - this.startY) > 10) {
                this.moved = true;
                this.longPressCancel();
            }
        }
    }"
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
                <button type="button" class="danger"
                        wire:click="deleteConversation({{ $conversation['id'] }})"
                        @click="open = false"
                        onclick="return confirm('Delete this conversation?')">
                    <i class="fa fa-trash"></i>
                    Delete chat
                </button>
            </div>
        </div>
    @endif
</div>
