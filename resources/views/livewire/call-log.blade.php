<div>
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    .cl-wrap { max-width: 900px; margin: 0 auto; padding: 24px 16px 60px; }
    .cl-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
    .cl-head h2 { color:#C1F11D; font-size:22px; font-weight:700; margin:0; display:flex; align-items:center; gap:10px; }
    .cl-filters { display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap; }
    .cl-filters button {
        background:#15191B; border:1px solid #23292B; color:#8b969a;
        padding:8px 14px; border-radius:20px; cursor:pointer; font-size:13px;
        transition: all .15s;
    }
    .cl-filters button:hover { color:#fff; border-color:#C1F11D; }
    .cl-filters button.active { background:#C1F11D; color:#000; border-color:#C1F11D; font-weight:600; }

    .cl-list { background:#15191B; border:1px solid #23292B; border-radius:10px; overflow:hidden; }
    .cl-row { display:flex; align-items:center; padding:14px 18px; border-bottom:1px solid #1c2224; transition:background .15s; }
    .cl-row:last-child { border-bottom:none; }
    .cl-row:hover { background:#181d1f; }

    .cl-avatar { width:44px; height:44px; border-radius:50%; background:#C1F11D; color:#000; display:flex; align-items:center; justify-content:center; font-weight:700; margin-right:14px; overflow:hidden; flex-shrink:0; }
    .cl-avatar img { width:100%; height:100%; object-fit:cover; }

    .cl-info { flex:1; min-width:0; }
    .cl-name { color:#fff; font-weight:600; font-size:15px; margin-bottom:4px; }
    .cl-name.missed { color:#ef4444; }
    .cl-meta { display:flex; align-items:center; gap:8px; font-size:12px; color:#8b969a; }
    .cl-meta i { font-size:11px; }
    .cl-meta .in  { color:#22c55e; }
    .cl-meta .out { color:#3b82f6; }
    .cl-meta .status-missed { color:#ef4444; }
    .cl-meta .status-declined { color:#eab308; }

    .cl-time { color:#8b969a; font-size:12px; text-align:right; margin-right:16px; }

    .cl-actions button {
        width:38px; height:38px; border-radius:50%; border:none;
        background:transparent; color:#C1F11D; cursor:pointer; font-size:15px;
        transition:background .15s;
    }
    .cl-actions button:hover { background:rgba(193,241,29,0.1); }

    .cl-empty { padding:60px 20px; text-align:center; color:#666; }
    .cl-empty i { font-size:48px; color:#2f3739; margin-bottom:16px; display:block; }
    .cl-empty h4 { color:#8b969a; font-size:16px; margin:0 0 6px; }
    .cl-empty p { color:#5f6d71; font-size:13px; margin:0; }

    .cl-back { color:#8b969a; text-decoration:none; font-size:14px; display:inline-flex; align-items:center; gap:6px; }
    .cl-back:hover { color:#C1F11D; }

    @media (max-width: 640px) {
        .cl-time { display:none; }
        .cl-row { padding:12px 14px; }
    }
</style>
@endpush

<div class="cl-wrap">
    <div class="cl-head">
        <h2><i class="fa fa-phone"></i> Call history</h2>
        <a class="cl-back" href="{{ route('user.chat') }}"><i class="fa fa-angle-left"></i> Back to messages</a>
    </div>

    <div class="cl-filters">
        <button type="button" wire:click="setFilter('all')" class="{{ $filter === 'all' ? 'active' : '' }}">All</button>
        <button type="button" wire:click="setFilter('missed')" class="{{ $filter === 'missed' ? 'active' : '' }}">Missed</button>
        <button type="button" wire:click="setFilter('incoming')" class="{{ $filter === 'incoming' ? 'active' : '' }}">Incoming</button>
        <button type="button" wire:click="setFilter('outgoing')" class="{{ $filter === 'outgoing' ? 'active' : '' }}">Outgoing</button>
    </div>

    <div class="cl-list">
        @forelse($calls as $c)
            <div class="cl-row" wire:key="call-{{ $c['id'] }}">
                <div class="cl-avatar">
                    @if(!empty($c['peer_avatar']))
                        <img src="{{ asset('storage/' . $c['peer_avatar']) }}" alt="">
                    @else
                        {{ strtoupper(substr($c['peer_name'] ?? '?', 0, 1)) }}
                    @endif
                </div>
                <div class="cl-info">
                    <div class="cl-name {{ $c['status'] === 'missed' ? 'missed' : '' }}">
                        {{ $c['peer_name'] }}
                    </div>
                    <div class="cl-meta">
                        @if($c['is_outgoing'])
                            <span class="out"><i class="fa fa-arrow-up"></i> Outgoing</span>
                        @else
                            <span class="in"><i class="fa fa-arrow-down"></i> Incoming</span>
                        @endif
                        <span>·</span>
                        <span>
                            <i class="fa fa-{{ $c['type'] === 'video' ? 'video' : 'phone' }}"></i>
                            {{ ucfirst($c['type']) }}
                        </span>
                        <span>·</span>
                        <span class="status-{{ $c['status'] }}">
                            @if($c['status'] === 'missed')       Missed
                            @elseif($c['status'] === 'declined') Declined
                            @elseif($c['status'] === 'ended')    {{ $c['duration'] ? gmdate('i:s', $c['duration']) : 'Ended' }}
                            @elseif($c['status'] === 'answered') In call
                            @elseif($c['status'] === 'ringing')  Ringing
                            @else                                {{ ucfirst($c['status']) }}
                            @endif
                        </span>
                    </div>
                </div>
                <div class="cl-time">
                    {{ \Carbon\Carbon::parse($c['started_at'])->diffForHumans() }}
                </div>
                <div class="cl-actions">
                    @if($c['peer_id'])
                        <a href="{{ route('user.chat.with', $c['peer_id']) }}" title="Open chat">
                            <button type="button"><i class="fa fa-comment"></i></button>
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="cl-empty">
                <i class="fa fa-phone-slash"></i>
                <h4>No calls yet</h4>
                <p>Your call history will appear here.</p>
            </div>
        @endforelse
    </div>
</div>
</div>
