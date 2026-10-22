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
    /* ---- Topbar ---- WhatsApp-style: minimize chip left, name+timer
       centered, add-participant chip right. The old flat "avatar + name
       + sub" row is hidden via CSS — the big centred avatar in the stage
       and the add-participant icon replace it. */
    .rtc-call .rtc-topbar {
        padding: 14px 16px;
        display: grid;
        /* 1fr | auto | 1fr so the name column stays truly centered in
           the viewport regardless of what sits on the left/right.
           With auto|1fr|auto the middle would absorb any slack from a
           missing right-side chip and skew the title visually. */
        grid-template-columns: 1fr auto 1fr;
        align-items: center;
        gap: 10px;
        color: #fff; font-size: 13px;
        background: transparent;
        border-bottom: none;
    }
    .rtc-call.maximized .rtc-topbar { padding: 18px 22px; font-size: 14px; }

    /* Hide the small inline topbar avatar — redundant now that the big
       stage avatar is the primary identity cue. */
    .rtc-call .rtc-topbar .rtc-avatar-lg { display: none; }

    /* Centred name + timer stack. .who-label is grid cell 2; the inner
       <div> wraps the name on top and the ringing/connected state below
       (state is hidden once the timer takes over). */
    .rtc-call .rtc-topbar .who-label {
        min-width: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-self: center;
        text-align: center;
        gap: 2px;
    }
    .rtc-call .rtc-topbar .who-label > div:last-child {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        min-width: 0;
        max-width: 100%;
    }
    .rtc-call .rtc-topbar .who-label > div:last-child > div:first-child {
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        font-size: 16px; font-weight: 600; color: #fff;
    }
    /* State line sits directly under the name — holds "Ringing…" /
       "Connecting…" during setup, then flips to the running timer
       (startTimer in webrtc-scripts updates both this element AND
       rtcTimer, so the position under the name always has the right
       text regardless of call phase). */
    .rtc-call .rtc-topbar .who-label > div:last-child > div:last-child {
        font-size: 13px;
        font-weight: 400;
        color: rgba(255,255,255,0.72);
        font-variant-numeric: tabular-nums;
    }

    /* The right-side timer is redundant now that the state line carries
       the elapsed time. Keep it in the DOM for backwards compatibility
       but hide it visually. */
    .rtc-call .rtc-topbar-right .rtc-timer { display: none !important; }

    /* Right cluster: quality pill (sampled stats), timer, add-participant. */
    .rtc-call .rtc-topbar-right {
        display: flex; align-items: center; gap: 8px; flex-shrink: 0;
        justify-self: end;
    }
    /* Compact quality pill in the small window — icon-only, text hidden;
       hover / tap reveals the full label. Maximized view keeps the label. */
    .rtc-call .rtc-quality { padding: 3px 8px; font-size: 11px; }
    .rtc-call:not(.maximized) .rtc-quality span:last-child { display: none; }
    .rtc-call.maximized .rtc-quality { padding: 4px 10px; font-size: 12px; }
    .rtc-call.maximized .rtc-quality span:last-child { display: inline; }
    .rtc-call .rtc-avatar-lg { width: 34px; height: 34px; border-radius: 50%; background: #C1F11D; color: #000; display: flex; align-items: center; justify-content: center; font-weight: 700; overflow: hidden; }
    .rtc-call .rtc-avatar-lg img { width: 100%; height: 100%; object-fit: cover; }
    /* Timer sits under the name — WhatsApp uses soft grey for the elapsed
       time so it reads as metadata, not a loud accent. */
    .rtc-call .rtc-timer { font-variant-numeric: tabular-nums; color: rgba(255,255,255,0.72); font-weight: 400; font-size: 13px; }
    .rtc-call.maximized .rtc-timer { font-size: 14px; }
    .rtc-call .rtc-quality {
        font-size: 12px; padding: 4px 10px; border-radius: 12px;
        display: inline-flex; align-items: center; gap: 6px;
    }

    /* Add-participant chip (top-right). Soft dark round, lights up on
       hover. Placeholder for the group-call feature. */
    .rtc-call .rtc-topbar-add {
        width: 38px; height: 38px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.08);
        color: #fff;
        cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background 150ms;
    }
    .rtc-call .rtc-topbar-add:hover { background: rgba(255,255,255,0.16); }
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
        width: 160px; height: 160px; border-radius: 50%;
        /* Soft neutral gradient so the initial-letter fallback looks like
           an intentional avatar, not a bright "missing image" chip. If the
           peer has a real avatar it's injected as an <img> that fills the
           circle via the img rule below. */
        background: linear-gradient(135deg, #3a3f44, #1f2327);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 60px;
        margin: 0 auto; overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.45);
    }
    .rtc-call.maximized .rtc-remote-audio-only .big-avatar {
        width: 220px; height: 220px; font-size: 84px;
    }
    .rtc-call .rtc-remote-audio-only .big-avatar img { width:100%; height:100%; object-fit:cover; }
    /* The topbar now carries the peer name; hide the duplicate name/sub
       under the avatar for the WhatsApp-style single-identity layout. */
    .rtc-call .rtc-remote-audio-only .callee-name,
    .rtc-call .rtc-remote-audio-only .callee-sub { display: none; }

    .rtc-call video.rtc-local {
        position: absolute; top: 12px; right: 12px;
        width: 90px; height: 130px; object-fit: cover; background: #111;
        border-radius: 8px; border: 2px solid rgba(255,255,255,0.2);
        transform: scaleX(-1);
    }
    .rtc-call.maximized video.rtc-local { top: 16px; right: 16px; width: 160px; height: 220px; border-radius: 10px; }
    .rtc-call video.rtc-local.audio-only { display: none; }

    /* ---- Control sheet (WhatsApp 3-column grid with labels) ----
       Dark rounded card at the bottom of the call panel. Each cell is
       a .rtc-ctrl that stacks the circular button + a text label below.
       Buttons the current state doesn't support (video toggle during
       voice call, switch during video call, etc.) are hidden by JS
       setting the inner <button>'s display:none; we rely on :has() to
       collapse the whole .rtc-ctrl so the grid still looks balanced. */
    .rtc-call .rtc-controls {
        padding: 18px 14px;
        background: #1C1F21;
        border-radius: 22px;
        margin: 0 14px 14px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 18px 10px;
        justify-items: center;
        align-items: start;
    }
    /* Maximized/desktop: cap the control sheet width and center it so
       the buttons sit next to each other with a decent inline gap,
       instead of being spread across the full viewport by the mobile
       3-column grid (which stretched each cell to ~1/3 of the screen
       on wide displays and pushed the End button to a second row). */
    .rtc-call.maximized .rtc-controls {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 22px 28px;
        max-width: 560px;
        margin: 0 auto calc(20px + env(safe-area-inset-bottom, 0px));
        padding: 22px 28px;
    }
    .rtc-ctrl {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }
    /* Collapse a cell only when ALL its buttons are hidden. The Video
       slot holds two buttons (rtcVideoBtn + rtcSwitchBtn) and only one
       is visible at a time; the cell should stay as long as either is
       live. :has() with :not() checks "no visible button inside". */
    .rtc-ctrl:not(:has(> button:not([style*="display:none"]):not([style*="display: none"]))) {
        display: none;
    }
    .rtc-ctrl-label {
        color: rgba(255,255,255,0.85);
        font-size: 12px;
        font-weight: 500;
        letter-spacing: 0.1px;
    }

    .rtc-call .rtc-btn {
        width: 54px; height: 54px; border-radius: 50%; border: none;
        background: #2A2E31; color: #fff; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center;
        transition: background 0.15s, transform 0.15s;
    }
    .rtc-call.maximized .rtc-btn { width: 60px; height: 60px; }
    .rtc-call .rtc-btn:hover { background: #343a3e; transform: translateY(-1px); }
    .rtc-call .rtc-btn.muted { background: #fff; color: #0a0a0a; }
    .rtc-call .rtc-btn.video-off { background: #fff; color: #0a0a0a; }
    /* Speaker ON = WhatsApp "active" state: white filled circle with
       dark icon, matching the Audio/Bluetooth chip in the reference. */
    .rtc-call .rtc-btn.speaker-on { background: #fff; color: #0a0a0a; }
    .rtc-call .rtc-btn.hangup { background: #ef4444; }
    .rtc-call .rtc-btn.hangup:hover { background: #dc2626; }
    /* Active "You're sharing your screen" state — WhatsApp's active-chip
       treatment: white filled circle, dark glyph, lime tint on the label. */
    .rtc-call .rtc-btn.sharing { background: #fff; color: #0a0a0a; }
    .rtc-ctrl:has(> .rtc-btn.sharing) .rtc-ctrl-label { color: #C1F11D; }

    /* Switch button pending state — WhatsApp-style: button dims and
       disables clicks while we wait for the peer's accept/reject reply,
       but the icon stays still (no spin). Matches WhatsApp's "quiet
       waiting" affordance rather than a loading animation. */
    .rtc-call #rtcSwitchBtn.is-pending { opacity: 0.55; pointer-events: none; }

    /* Incoming switch-request prompt. Positioned fixed, above everything,
       with a dark overlay backdrop. Hidden until .open is added.
       z-index must be ABOVE .rtc-call (100040) — on mobile .rtc-call goes
       edge-to-edge and would otherwise cover this prompt entirely. */
    .rtc-switch-prompt {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.72);
        z-index: 100070;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .rtc-switch-prompt.open { display: flex; }
    .rtc-switch-prompt-card {
        background: #131616;
        border: 1px solid #242B2D;
        border-radius: 14px;
        padding: 24px 24px 18px;
        max-width: 360px;
        width: 100%;
        text-align: center;
        color: #fff;
    }
    .rtc-switch-prompt-icon {
        width: 56px; height: 56px; border-radius: 50%;
        background: rgba(193,241,29,0.14);
        color: #C1F11D;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 22px;
        margin-bottom: 12px;
    }
    .rtc-switch-prompt-title { font-size: 17px; font-weight: 600; margin-bottom: 6px; }
    .rtc-switch-prompt-sub { color: #AAAEB5; font-size: 13.5px; margin-bottom: 18px; line-height: 1.45; }
    .rtc-switch-prompt-actions { display: flex; gap: 10px; }
    .rtc-switch-btn {
        flex: 1 1 0;
        border: none;
        border-radius: 999px;
        padding: 11px 16px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
    }
    .rtc-switch-btn--reject { background: #2a2d30; color: #fff; }
    .rtc-switch-btn--accept { background: #C1F11D; color: #0a0a0a; }

    /* Minimize chip (top-left). Round soft-dark button mirroring the
       add-participant chip on the opposite side. */
    .rtc-call .rtc-window-btns { display: inline-flex; gap: 6px; justify-self: start; }
    .rtc-call .rtc-window-btns button {
        width: 38px; height: 38px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.08);
        color: #fff;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background 150ms;
    }
    .rtc-call .rtc-window-btns button:hover { background: rgba(255,255,255,0.16); color: #fff; }

    /* WhatsApp-style call toast — soft blurred pill, no harsh border.
       Used for switch-request feedback, missed-call errors, etc.
       `.persistent` disables the auto-dismiss animation so banners that
       should stick around (e.g. "waiting for peer to accept") hold their
       place until explicitly dismissed. */
    .rtc-toast {
        position: fixed;
        top: 24px;
        left: 50%;
        transform: translate(-50%, -10px);
        background: rgba(20, 22, 24, 0.82);
        backdrop-filter: blur(14px) saturate(140%);
        -webkit-backdrop-filter: blur(14px) saturate(140%);
        color: #fff;
        padding: 11px 20px;
        border-radius: 999px;
        font-size: 13.5px;
        font-weight: 500;
        letter-spacing: 0.1px;
        line-height: 1.35;
        max-width: min(460px, calc(100vw - 32px));
        text-align: center;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.4);
        z-index: 100060;
        opacity: 0;
        pointer-events: none;
        display: none;
    }
    .rtc-toast.open { display: inline-flex; align-items: center; gap: 10px; animation: rtcToast 4s forwards; }
    .rtc-toast.persistent {
        animation: rtcToastIn 220ms ease-out forwards;
        pointer-events: auto;
    }
    @keyframes rtcToast {
        0%          { opacity: 0; transform: translate(-50%, -12px) scale(0.96); }
        12%, 88%    { opacity: 1; transform: translate(-50%, 0)      scale(1); }
        100%        { opacity: 0; transform: translate(-50%, -8px)   scale(0.98); }
    }
    @keyframes rtcToastIn {
        from { opacity: 0; transform: translate(-50%, -12px) scale(0.96); }
        to   { opacity: 1; transform: translate(-50%, 0)      scale(1); }
    }

    /* Inside the persistent banner: a tiny breathing dot (indicates
       "waiting"), the message text, and an optional cancel button that
       lets the user back out of the pending request without hanging up. */
    .rtc-toast .rtc-toast-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #C1F11D;
        flex-shrink: 0;
        animation: rtcToastDot 1.4s ease-in-out infinite;
    }
    @keyframes rtcToastDot {
        0%, 100% { opacity: 0.4; transform: scale(0.85); }
        50%      { opacity: 1;   transform: scale(1.1); }
    }
    .rtc-toast .rtc-toast-msg { flex: 1 1 auto; }
    .rtc-toast .rtc-toast-cancel {
        flex-shrink: 0;
        background: transparent;
        border: none;
        color: #C1F11D;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        padding: 2px 4px;
        border-radius: 6px;
        margin-left: 4px;
    }
    .rtc-toast .rtc-toast-cancel:hover { color: #d9ff4a; background: rgba(193,241,29,0.08); }

    /* Chat header call buttons */
    .chat-call-buttons { display: inline-flex; gap: 6px; margin-right: 8px; }
    .chat-call-buttons button {
        background: transparent; border: none; color: #C1F11D; font-size: 18px;
        padding: 6px 10px; cursor: pointer; border-radius: 6px;
    }
    .chat-call-buttons button:hover { background: rgba(193,241,29,0.1); }
    .chat-call-buttons button:disabled { opacity: 0.4; cursor: not-allowed; }

    /* Phone-size overrides — the WhatsApp layout is applied universally
       above, so here we only need to make the call go edge-to-edge and
       respect iOS safe-area insets. */
    @media (max-width: 900px) {
        .rtc-call {
            top: 0; right: 0; bottom: 0; left: 0;
            width: auto; height: auto;
            max-height: none;
            border-radius: 0;
            border: none;
            box-shadow: none;
            background: #0a0a0a;
        }
        .rtc-call .rtc-topbar {
            padding: calc(18px + env(safe-area-inset-top, 0px)) 16px 10px;
        }
        .rtc-call .rtc-controls {
            margin: 0 12px calc(12px + env(safe-area-inset-bottom, 0px));
        }
        /* Local self-view: compact and tucked under the topbar. */
        .rtc-call video.rtc-local {
            top: calc(80px + env(safe-area-inset-top, 0px));
            right: 14px;
            width: 100px;
            height: 140px;
            border-radius: 12px;
        }
    }

    @media (max-width: 640px) {
        /* Full-screen incoming call UI on mobile — WhatsApp-style.
           Replaces the previous small-top-banner so the user can see the
           caller clearly and tap the big Accept/Decline buttons without
           needing to hunt for a tiny banner at the top of the screen. */
        .rtc-incoming {
            top: 0; right: 0; left: 0; bottom: 0;
            min-width: unset;
            padding: 0;
            border: none;
            border-radius: 0;
            background: linear-gradient(180deg, #10151A 0%, #050709 100%);
            box-shadow: none;
            animation: none; /* kill the lime pulse — too busy at full-screen */
        }
        .rtc-incoming.open { display: flex; flex-direction: column; }
        .rtc-incoming .who {
            flex: 1;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 24px;
            margin: 0;
            padding: 48px 24px 32px;
            text-align: center;
        }
        .rtc-incoming .avatar {
            width: 140px;
            height: 140px;
            font-size: 56px;
            box-shadow: 0 0 0 6px rgba(193, 241, 29, 0.15),
                        0 10px 40px rgba(0, 0, 0, 0.5);
            animation: rtcIncomingAvatarPulse 1.8s ease-in-out infinite;
        }
        .rtc-incoming .name { font-size: 26px; font-weight: 600; }
        .rtc-incoming .sub { font-size: 15px; margin-top: 4px; }
        .rtc-incoming .actions {
            gap: 70px;
            padding: 0 24px calc(48px + env(safe-area-inset-bottom, 0px));
            justify-content: center;
        }
        .rtc-incoming .btn-accept,
        .rtc-incoming .btn-decline {
            flex: 0 0 72px;
            width: 72px; height: 72px;
            border-radius: 50%;
            padding: 0;
            font-size: 26px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }
        .rtc-incoming .btn-accept  { animation: rtcAcceptNudge 1.6s ease-in-out infinite; }
        .rtc-incoming .btn-decline { animation: rtcDeclineShake 1.6s ease-in-out infinite; }
        .rtc-incoming .btn-accept i,
        .rtc-incoming .btn-decline i { font-size: 24px; }
        /* Hide the "Accept" / "Decline" text labels on the buttons —
           icon-only matches WhatsApp. The screen-reader name is covered
           by the button's own semantics. */
        .rtc-incoming .btn-accept,
        .rtc-incoming .btn-decline { font-size: 0; }
        .rtc-incoming .btn-accept i,
        .rtc-incoming .btn-decline i { font-size: 24px; }

        /* Push the toast below the call topbar on phone screens so the
           "Asking … to switch to video" pill clears the peer name header. */
        .rtc-toast {
            top: calc(92px + env(safe-area-inset-top, 0px));
            max-width: calc(100vw - 36px);
            padding: 10px 18px;
            font-size: 13px;
        }
    }
    @keyframes rtcIncomingAvatarPulse {
        0%, 100% { box-shadow: 0 0 0 6px rgba(193, 241, 29, 0.15),  0 10px 40px rgba(0,0,0,0.5); }
        50%      { box-shadow: 0 0 0 18px rgba(193, 241, 29, 0.05), 0 10px 40px rgba(0,0,0,0.5); }
    }
    @keyframes rtcAcceptNudge {
        0%, 100% { transform: translateY(0); }
        50%      { transform: translateY(-6px); }
    }
    @keyframes rtcDeclineShake {
        0%, 100% { transform: rotate(0); }
        25%      { transform: rotate(-12deg); }
        75%      { transform: rotate(12deg); }
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

{{-- Full-screen in-call. Layout mirrors WhatsApp mobile: minimize chip
     on the top-left, peer name + call timer stacked and centered, add-
     participant chip on the top-right. The large circular avatar sits
     in the stage, and a dark rounded control sheet at the bottom holds
     the action buttons as a 3-column grid, each with its own label. --}}
<div class="rtc-call" id="rtcCall" role="dialog" aria-label="Active call">
    <div class="rtc-topbar">
        {{-- LEFT: minimize chip (toggles .maximized class via JS). --}}
        <div class="rtc-window-btns">
            <button type="button" id="rtcMaximizeBtn" title="Minimize" aria-label="Minimize call window">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <polyline points="4 14 10 14 10 20"></polyline>
                    <polyline points="20 10 14 10 14 4"></polyline>
                    <line x1="14" y1="10" x2="21" y2="3"></line>
                    <line x1="3" y1="21" x2="10" y2="14"></line>
                </svg>
            </button>
        </div>

        {{-- CENTER: title + timer. The small topbar avatar is still in the
             DOM for the compact-floating-card look but hidden by CSS so
             the WhatsApp-style name/timer stack reads cleanly. --}}
        <div class="who-label">
            <div class="rtc-avatar-lg" id="rtcCallAvatar">?</div>
            <div>
                <div style="font-weight:600;" id="rtcCallName">—</div>
                <div style="font-size:11px; color:#8b969a;" id="rtcCallState">Ringing…</div>
            </div>
        </div>

        {{-- RIGHT: quality pill (hidden until stats sampled), running
             timer, and the Add-participant chip. The chip is a placeholder
             for the group-call feature — click currently just shows a toast. --}}
        <div class="rtc-topbar-right">
            <span class="rtc-quality good" id="rtcQuality" style="display:none;">
                <span class="dot"></span> <span id="rtcQualityLabel">Good</span>
            </span>
            <span class="rtc-timer" id="rtcTimer" style="display:none;">00:00</span>
            {{-- Add-participant removed — requires an SFU for group calls,
                 which isn't on this project's roadmap right now. --}}
        </div>
    </div>

    <div class="rtc-stage" id="rtcStage">
        {{-- Audio-only placeholder shown when neither peer is sending video.
             Name/sub lines are hidden by CSS because the topbar now carries
             the title — matches the WhatsApp layout. --}}
        <div class="rtc-remote-audio-only" id="rtcAudioOnly" style="display:none;">
            <div class="big-avatar" id="rtcAudioAvatar">?</div>
            <div class="callee-name" id="rtcAudioName">—</div>
            <div class="callee-sub" id="rtcAudioSub">Voice call</div>
        </div>
        <video class="rtc-remote" id="rtcRemoteVideo" autoplay playsinline></video>
        <video class="rtc-local"  id="rtcLocalVideo"  autoplay playsinline muted></video>
        {{-- Dedicated audio element for remote AUDIO tracks — mobile browsers
             refuse to autoplay audio through a <video> tag when there's no
             video track (voice-only calls), leaving remote voice silent.
             Routing audio here (see ontrack in webrtc-scripts) fixes it. --}}
        <audio id="rtcRemoteAudio" autoplay playsinline style="display:none;"></audio>
    </div>

    {{-- Control sheet. Each button lives inside a .rtc-ctrl wrapper that
         stacks the circle + a text label below it, matching WhatsApp's
         "Audio / Video / Mute — More / Share / End" grid. Buttons that
         are irrelevant to the current call state (voice-only vs video,
         pre-connect vs connected) are hidden by JS via style.display. --}}
    <div class="rtc-controls">
        <div class="rtc-ctrl">
            <button type="button" class="rtc-btn" id="rtcSpeakerBtn" title="Toggle speaker" style="display:none;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M3 10v4a1 1 0 0 0 1 1h3l5 4V5L7 9H4a1 1 0 0 0-1 1z"/><path d="M16 7.5c1.8 1 3 2.8 3 4.9s-1.2 3.9-3 4.9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M18 4c3 1.5 5 4.7 5 8.4s-2 6.9-5 8.4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
            <span class="rtc-ctrl-label">Audio</span>
        </div>

        {{-- Video / switch — the explicit "video toggle" (rtcVideoBtn) is
             visible when the current call type is video; "switch" (rtcSwitchBtn)
             is visible otherwise and sends the upgrade request. Both share
             the same visual slot. --}}
        <div class="rtc-ctrl">
            <button type="button" class="rtc-btn" id="rtcVideoBtn" title="Toggle camera" style="display:none;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M15 8v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2zm7-1l-5 3v4l5 3V7z"/></svg>
            </button>
            <button type="button" class="rtc-btn" id="rtcSwitchBtn" title="Switch to video" style="display:none;">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M15 8v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2zm7-1l-5 3v4l5 3V7z"/></svg>
            </button>
            <span class="rtc-ctrl-label">Video</span>
        </div>

        <div class="rtc-ctrl">
            <button type="button" class="rtc-btn" id="rtcMuteBtn" title="Mute microphone" aria-label="Mute">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12 15a3 3 0 0 0 3-3V6a3 3 0 1 0-6 0v6a3 3 0 0 0 3 3z"/><path d="M19 11a1 1 0 1 0-2 0 5 5 0 1 1-10 0 1 1 0 1 0-2 0 7 7 0 0 0 6 6.92V20H8a1 1 0 1 0 0 2h8a1 1 0 1 0 0-2h-3v-2.08A7 7 0 0 0 19 11z"/></svg>
            </button>
            <span class="rtc-ctrl-label">Mute</span>
        </div>

        {{-- "More" removed — the actions it would hold aren't built yet. --}}

        <div class="rtc-ctrl">
            <button type="button" class="rtc-btn" id="rtcShareBtn" title="Share screen" aria-label="Share screen">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="14" rx="2"></rect><polyline points="8 10 12 6 16 10"></polyline><line x1="12" y1="6" x2="12" y2="16"></line></svg>
            </button>
            <span class="rtc-ctrl-label" id="rtcShareLabel">Share</span>
        </div>

        <div class="rtc-ctrl">
            <button type="button" class="rtc-btn hangup" id="rtcHangupBtn" title="Hang up" aria-label="End call">
                {{-- Material-style "call_end" glyph (centered, no transform
                     offsets that pushed the previous handset icon off-screen). --}}
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
                    <path d="M12 9c-1.6 0-3.15.25-4.6.72v3.1c0 .39-.23.74-.56.9-.98.49-1.87 1.12-2.66 1.85-.18.18-.43.28-.7.28-.28 0-.53-.11-.71-.29L.29 13.08a.991.991 0 0 1-.29-.7c0-.28.11-.53.29-.71C3.34 8.78 7.46 7 12 7s8.66 1.78 11.71 4.67c.18.18.29.43.29.71 0 .28-.11.53-.29.71l-2.48 2.48c-.18.18-.43.29-.71.29-.27 0-.52-.1-.7-.28-.79-.74-1.69-1.36-2.67-1.85-.33-.16-.56-.5-.56-.9v-3.1C15.15 9.25 13.6 9 12 9z"/>
                </svg>
            </button>
            <span class="rtc-ctrl-label">End</span>
        </div>
    </div>
</div>

{{-- Incoming switch-request prompt: peer wants to upgrade/downgrade. --}}
<div class="rtc-switch-prompt" id="rtcSwitchPrompt">
    <div class="rtc-switch-prompt-card">
        <div class="rtc-switch-prompt-icon"><i class="fa fa-video" id="rtcSwitchPromptIcon"></i></div>
        <div class="rtc-switch-prompt-title" id="rtcSwitchPromptTitle">Switch to video?</div>
        <div class="rtc-switch-prompt-sub" id="rtcSwitchPromptSub">The other person wants to turn on video.</div>
        <div class="rtc-switch-prompt-actions">
            <button type="button" class="rtc-switch-btn rtc-switch-btn--reject" id="rtcSwitchRejectBtn">Decline</button>
            <button type="button" class="rtc-switch-btn rtc-switch-btn--accept" id="rtcSwitchAcceptBtn">Accept</button>
        </div>
    </div>
</div>

<div class="rtc-toast" id="rtcToast">—</div>

{{-- Hidden ringback tones (data URIs kept short to avoid layout bloat) --}}
<audio id="rtcRingtone" loop preload="auto">
    <source src="{{ asset('assets/newtheme/ringtone.mp3') }}" type="audio/mpeg">
</audio>
