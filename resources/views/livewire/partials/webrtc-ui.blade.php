{{-- WebRTC call UI: incoming-call modal + in-call full-screen overlay.
     Included from chat.blade.php. Rendered once per page, controlled entirely
     by resources/views/livewire/partials/webrtc-scripts.blade.php. --}}
<style>
    /* Incoming call modal */
    .rtc-incoming {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 100050;
        background: #15191B;
        border: 1px solid #23292B;
        border-radius: 14px;
        padding: 18px 22px 16px;
        box-shadow: 0 12px 40px rgba(0,0,0,0.6);
        display: none;
        min-width: 320px;
        color: #fff;
        animation: rtcIncomingPulse 1.5s ease-in-out infinite;
    }
    .rtc-incoming.open { display: block; }
    @keyframes rtcIncomingPulse {
        0%,100% { box-shadow: 0 12px 40px rgba(0,0,0,0.6), 0 0 0 0 rgba(193,241,29,0.35); }
        50%     { box-shadow: 0 12px 40px rgba(0,0,0,0.6), 0 0 0 14px rgba(193,241,29,0); }
    }
    .rtc-incoming .who { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .rtc-incoming .avatar { width: 46px; height: 46px; border-radius: 50%; background: #C1F11D; color: #000; display: flex; align-items: center; justify-content: center; font-weight: 700; overflow: hidden; }
    .rtc-incoming .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .rtc-incoming .name { font-weight: 600; font-size: 15px; }
    .rtc-incoming .sub { font-size: 12px; color: #8b969a; }
    .rtc-incoming .actions { display: flex; gap: 10px; }
    .rtc-incoming .btn-accept, .rtc-incoming .btn-decline {
        flex: 1; border: none; padding: 10px 14px; border-radius: 8px;
        font-size: 13px; font-weight: 600; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    }
    .rtc-incoming .btn-accept { background: #22c55e; color: #fff; }
    .rtc-incoming .btn-decline { background: #ef4444; color: #fff; }

    /* Floating call window (default) — small card in bottom-right, similar
       to WhatsApp Web's minimized call. Toggle `.rtc-call.maximized` for the
       old full-screen layout. */
    .rtc-call {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 340px;
        height: 500px;
        max-height: calc(100vh - 40px);
        z-index: 100040;
        background: linear-gradient(180deg, #060a0a 0%, #0d1315 100%);
        border-radius: 16px;
        border: 1px solid #2a2f31;
        box-shadow: 0 20px 60px rgba(0,0,0,0.55);
        display: none; flex-direction: column;
        overflow: hidden;
    }
    .rtc-call.open { display: flex; }
    .rtc-call.maximized {
        top: 0; right: 0; bottom: 0; left: 0;
        width: auto; height: auto;
        max-height: none;
        border-radius: 0;
        border: none;
        box-shadow: none;
    }
    .rtc-call .rtc-topbar {
        padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;
        gap: 8px;
        color: #fff; font-size: 13px;
        background: rgba(0,0,0,0.25);
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }
    .rtc-call.maximized .rtc-topbar { padding: 16px 22px; font-size: 14px; }
    .rtc-call .rtc-topbar .who-label { min-width: 0; flex: 1; }
    .rtc-call .rtc-topbar .who-label > div:last-child { min-width: 0; overflow: hidden; }
    .rtc-call .rtc-topbar .who-label > div:last-child > div:first-child {
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .rtc-call .rtc-topbar > div:last-child {
        display: flex; align-items: center; gap: 8px; flex-shrink: 0;
    }
    /* Compact quality pill in the small window — icon-only, text hidden;
       hover / tap reveals the full label. Maximized view keeps the label. */
    .rtc-call .rtc-quality { padding: 3px 8px; font-size: 11px; }
    .rtc-call:not(.maximized) .rtc-quality span:last-child { display: none; }
    .rtc-call.maximized .rtc-quality { padding: 4px 10px; font-size: 12px; }
    .rtc-call.maximized .rtc-quality span:last-child { display: inline; }
    .rtc-call .rtc-topbar .who-label { display: flex; align-items: center; gap: 10px; }
    .rtc-call .rtc-avatar-lg { width: 34px; height: 34px; border-radius: 50%; background: #C1F11D; color: #000; display: flex; align-items: center; justify-content: center; font-weight: 700; overflow: hidden; }
    .rtc-call .rtc-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    .rtc-call .rtc-timer { font-variant-numeric: tabular-nums; color: #C1F11D; font-weight: 600; }
    .rtc-call .rtc-quality {
        font-size: 12px; padding: 4px 10px; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .rtc-call .rtc-quality .dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; }
    .rtc-call .rtc-quality.good  { background: rgba(34,197,94,0.15); color: #22c55e; }
    .rtc-call .rtc-quality.fair  { background: rgba(234,179,8,0.15); color: #eab308; }
    .rtc-call .rtc-quality.fair .dot { background: #eab308; }
    .rtc-call .rtc-quality.poor  { background: rgba(239,68,68,0.15); color: #ef4444; }
    .rtc-call .rtc-quality.poor .dot { background: #ef4444; animation: rtcPulse 1s infinite ease-in-out; }
    @keyframes rtcPulse { 0%,100% { opacity: 1; } 50% { opacity: 0.35; } }

    .rtc-call .rtc-stage {
        flex: 1; position: relative; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
    }
    .rtc-call video.rtc-remote {
        width: 100%; height: 100%; object-fit: cover; background: #000;
    }
    .rtc-call .rtc-remote-audio-only {
        text-align: center; color: #fff;
    }
    .rtc-call .rtc-remote-audio-only .big-avatar {
        width: 96px; height: 96px; border-radius: 50%; background: #C1F11D; color: #000;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 40px;
        margin: 0 auto 16px; overflow: hidden;
    }
    .rtc-call.maximized .rtc-remote-audio-only .big-avatar {
        width: 140px; height: 140px; font-size: 54px; margin-bottom: 24px;
    }
    .rtc-call .rtc-remote-audio-only .callee-name { font-size: 17px; margin-bottom: 4px; }
    .rtc-call.maximized .rtc-remote-audio-only .callee-name { font-size: 20px; margin-bottom: 6px; }
    .rtc-call .rtc-remote-audio-only .big-avatar img { width:100%; height:100%; object-fit:cover; }
    .rtc-call .rtc-remote-audio-only .callee-name { font-size: 20px; font-weight: 600; margin-bottom: 6px; }
    .rtc-call .rtc-remote-audio-only .callee-sub { font-size: 13px; color: #8b969a; }

    .rtc-call video.rtc-local {
        position: absolute; top: 12px; right: 12px;
        width: 90px; height: 130px; object-fit: cover; background: #111;
        border-radius: 8px; border: 2px solid rgba(255,255,255,0.2);
        transform: scaleX(-1);
    }
    .rtc-call.maximized video.rtc-local { top: 16px; right: 16px; width: 160px; height: 220px; border-radius: 10px; }
    .rtc-call video.rtc-local.audio-only { display: none; }

    .rtc-call .rtc-controls {
        padding: 14px 12px; display: flex; align-items: center; justify-content: center; gap: 10px;
        background: rgba(0,0,0,0.35);
        flex-wrap: wrap;
    }
    .rtc-call.maximized .rtc-controls { padding: 22px; gap: 16px; }
    .rtc-call .rtc-btn {
        width: 44px; height: 44px; border-radius: 50%; border: none;
        background: #232a2c; color: #fff; cursor: pointer; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        transition: transform 0.15s;
    }
    .rtc-call.maximized .rtc-btn { width: 56px; height: 56px; font-size: 20px; }
    .rtc-call .rtc-btn:hover { transform: scale(1.05); }
    .rtc-call .rtc-btn.muted { background: #444; color: #C1F11D; }
    .rtc-call .rtc-btn.video-off { background: #444; color: #C1F11D; }
    .rtc-call .rtc-btn.speaker-on { background: #C1F11D; color: #000; }
    .rtc-call .rtc-btn.hangup { background: #ef4444; width: 52px; height: 52px; font-size: 20px; }
    .rtc-call.maximized .rtc-btn.hangup { width: 64px; height: 64px; font-size: 24px; }

    /* Header window controls (maximize / minimize toggle) */
    .rtc-call .rtc-window-btns { display: inline-flex; gap: 6px; }
    .rtc-call .rtc-window-btns button {
        width: 26px; height: 26px; border-radius: 6px; border: none;
        background: rgba(255,255,255,0.08); color: #ccc; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px;
    }
    .rtc-call .rtc-window-btns button:hover { background: rgba(255,255,255,0.15); color: #fff; }

    /* Toast for missed calls / errors */
    .rtc-toast {
        position: fixed; top: 24px; left: 50%; transform: translateX(-50%);
        background: #232a2c; color: #fff; padding: 12px 20px; border-radius: 8px;
        font-size: 13px; z-index: 100060; display: none;
        border: 1px solid #333;
    }
    .rtc-toast.open { display: block; animation: rtcToast 4s forwards; }
    @keyframes rtcToast {
        0% { opacity: 0; transform: translate(-50%, -10px); }
        10%,90% { opacity: 1; transform: translate(-50%, 0); }
        100% { opacity: 0; transform: translate(-50%, -10px); }
    }

    /* Chat header call buttons */
    .chat-call-buttons { display: inline-flex; gap: 6px; margin-right: 8px; }
    .chat-call-buttons button {
        background: transparent; border: none; color: #C1F11D; font-size: 18px;
        padding: 6px 10px; cursor: pointer; border-radius: 6px;
    }
    .chat-call-buttons button:hover { background: rgba(193,241,29,0.1); }
    .chat-call-buttons button:disabled { opacity: 0.4; cursor: not-allowed; }

    @media (max-width: 640px) {
        .rtc-call video.rtc-local { width: 110px; height: 150px; top: 12px; right: 12px; }
        .rtc-incoming { top: 16px; right: 12px; left: 12px; min-width: unset; }
    }
</style>

{{-- Incoming call banner --}}
<div class="rtc-incoming" id="rtcIncoming" role="dialog" aria-label="Incoming call">
    <div class="who">
        <div class="avatar" id="rtcIncomingAvatar">?</div>
        <div>
            <div class="name" id="rtcIncomingName">—</div>
            <div class="sub"><span id="rtcIncomingType">Voice</span> call…</div>
        </div>
    </div>
    <div class="actions">
        <button type="button" class="btn-accept" id="rtcAcceptBtn">
            <i class="fa fa-phone"></i> Accept
        </button>
        <button type="button" class="btn-decline" id="rtcDeclineBtn">
            <i class="fa fa-phone-slash"></i> Decline
        </button>
    </div>
</div>

{{-- Full-screen in-call --}}
<div class="rtc-call" id="rtcCall" role="dialog" aria-label="Active call">
    <div class="rtc-topbar">
        <div class="who-label">
            <div class="rtc-avatar-lg" id="rtcCallAvatar">?</div>
            <div>
                <div style="font-weight:600;" id="rtcCallName">—</div>
                <div style="font-size:11px; color:#8b969a;" id="rtcCallState">Ringing…</div>
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <span class="rtc-quality good" id="rtcQuality" style="display:none;">
                <span class="dot"></span> <span id="rtcQualityLabel">Good</span>
            </span>
            <span class="rtc-timer" id="rtcTimer" style="display:none;">00:00</span>
            <div class="rtc-window-btns">
                <button type="button" id="rtcMaximizeBtn" title="Maximize" aria-label="Maximize call window">
                    <i class="fa fa-expand"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="rtc-stage" id="rtcStage">
        {{-- Audio-only placeholder shown when neither peer is sending video --}}
        <div class="rtc-remote-audio-only" id="rtcAudioOnly" style="display:none;">
            <div class="big-avatar" id="rtcAudioAvatar">?</div>
            <div class="callee-name" id="rtcAudioName">—</div>
            <div class="callee-sub" id="rtcAudioSub">Voice call</div>
        </div>
        <video class="rtc-remote" id="rtcRemoteVideo" autoplay playsinline></video>
        <video class="rtc-local"  id="rtcLocalVideo"  autoplay playsinline muted></video>
        {{-- Dedicated audio element for remote AUDIO tracks. Mobile browsers
             (Chrome Android, Safari iOS, Samsung Internet) often refuse to
             autoplay audio through a <video> element that has no video track
             — the remote voice ends up silent. Routing audio tracks here
             (see ontrack handler in webrtc-scripts.blade.php) fixes voice
             calls on mobile. autoplay + playsinline are BOTH required. --}}
        <audio id="rtcRemoteAudio" autoplay playsinline style="display:none;"></audio>
    </div>

    <div class="rtc-controls">
        <button type="button" class="rtc-btn" id="rtcMuteBtn" title="Mute microphone">
            <i class="fa fa-microphone"></i>
        </button>
        <button type="button" class="rtc-btn" id="rtcSpeakerBtn" title="Toggle speaker" style="display:none;">
            <i class="fa fa-volume-up"></i>
        </button>
        <button type="button" class="rtc-btn" id="rtcVideoBtn" title="Toggle camera" style="display:none;">
            <i class="fa fa-video"></i>
        </button>
        <button type="button" class="rtc-btn hangup" id="rtcHangupBtn" title="Hang up">
            <i class="fa fa-phone-slash"></i>
        </button>
    </div>
</div>

<div class="rtc-toast" id="rtcToast">—</div>

{{-- Hidden ringback tones (data URIs kept short to avoid layout bloat) --}}
<audio id="rtcRingtone" loop preload="auto">
    <source src="{{ asset('assets/newtheme/ringtone.mp3') }}" type="audio/mpeg">
</audio>
