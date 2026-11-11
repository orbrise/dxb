{{--
    WhatsApp-style "typing…" bubble, shared by /my-chat and the admin Support
    Inbox. Server side: App\Support\ChatTyping + ChatTypingController.

    Each page renders its own row (outside the morphing message list, with
    wire:ignore so Livewire re-renders don't reset it):
        <div class="ev-typing-row" id="…" wire:ignore aria-live="polite">
            <div class="ev-typing-bubble"><span></span><span></span><span></span></div>
        </div>
    and themes it with --ev-typing-bg / --ev-typing-dot, then calls
    EvTyping.input() from its composer and EvTyping.watch() for the open chat.
--}}
<style>
    .ev-typing-row { display: none; flex: none; }
    .ev-typing-row.is-on { display: flex; }
    .ev-typing-bubble {
        position: relative; display: inline-flex; align-items: center; gap: 4px;
        margin-left: 8px; padding: 12px 14px; border-radius: 0 8px 8px 8px;
        background: var(--ev-typing-bg, #fff);
        box-shadow: 0 1px .5px rgba(11, 20, 26, .13);
        animation: evTypingIn .18s ease-out;
    }
    /* The tail at the top-left corner, as on WhatsApp's incoming bubbles. */
    .ev-typing-bubble::before {
        content: ""; position: absolute; top: 0; left: -8px; width: 8px; height: 13px;
        background: inherit; clip-path: polygon(0 0, 100% 0, 100% 100%);
    }
    .ev-typing-bubble span {
        width: 7px; height: 7px; border-radius: 50%;
        background: var(--ev-typing-dot, #8696a0);
        animation: evTypingDot 1.3s ease-in-out infinite;
    }
    .ev-typing-bubble span:nth-child(2) { animation-delay: .18s; }
    .ev-typing-bubble span:nth-child(3) { animation-delay: .36s; }
    @keyframes evTypingDot {
        0%, 60%, 100% { opacity: .35; transform: translateY(0) scale(.85); }
        30% { opacity: 1; transform: translateY(-2px) scale(1); }
    }
    @keyframes evTypingIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
    @media (prefers-reduced-motion: reduce) {
        .ev-typing-bubble, .ev-typing-bubble span { animation: none; }
        .ev-typing-bubble span { opacity: .6; }
    }
</style>
<script>
(function () {
    if (window.EvTyping) return;

    var PING_MS = 2000;   // the server keeps a ping for 5 s (ChatTyping::TTL)
    var POLL_MS = 2000;
    var sent = { conv: 0, at: 0 };
    var watcher = null, pollTimer = null, switchTimer = null, inFlight = false, shownConv = 0;

    function token() {
        if (window.SessionRecovery && typeof window.SessionRecovery.getToken === 'function') {
            var t = window.SessionRecovery.getToken();
            if (t) return t;
        }
        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function post(conv, typing) {
        try {
            fetch('/chat/typing/' + conv, {
                method: 'POST',
                credentials: 'same-origin',
                keepalive: !typing,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token()
                },
                body: JSON.stringify({ typing: typing ? 1 : 0 })
            }).catch(function () {});
        } catch (e) {}
    }

    // Composer input: ping while there's text, at most every PING_MS.
    function input(conv, value) {
        conv = Number(conv) || 0;
        if (sent.conv && sent.conv !== conv) stop();
        if (!conv) return;
        if (!String(value || '').trim()) { stop(); return; }
        var now = Date.now();
        if (sent.conv === conv && now - sent.at < PING_MS) return;
        sent = { conv: conv, at: now };
        post(conv, true);
    }

    // Box emptied without sending: tell the other side now.
    function stop() {
        if (!sent.conv) return;
        var conv = sent.conv;
        sent = { conv: 0, at: 0 };
        post(conv, false);
    }

    // Message sent: the server already cleared it (Message created hook).
    function reset() { sent = { conv: 0, at: 0 }; }

    function nearBottom(scroller) {
        return !scroller || scroller.scrollHeight - scroller.scrollTop - scroller.clientHeight < 80;
    }

    function show(conv) {
        var row = watcher && watcher.row();
        if (!row || row.classList.contains('is-on')) { shownConv = conv; return; }
        var scroller = watcher.scroller && watcher.scroller();
        var stick = nearBottom(scroller);
        row.classList.add('is-on');
        shownConv = conv;
        // The row takes room from the message list; keep the newest message in view.
        if (stick && scroller) scroller.scrollTop = scroller.scrollHeight;
    }

    function hide() {
        shownConv = 0;
        var row = watcher && watcher.row();
        if (row) row.classList.remove('is-on');
    }

    function currentConv() {
        return watcher ? (Number(watcher.convId()) || 0) : 0;
    }

    function tick() {
        if (!watcher || inFlight || document.hidden) return;
        var conv = currentConv();
        if (!conv || !watcher.row()) { hide(); return; }
        inFlight = true;
        fetch('/chat/typing/' + conv, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (currentConv() !== conv) return;   // switched chats meanwhile
                if (data && data.typing) {
                    show(conv);
                } else if (shownConv) {
                    hide();
                    // They stopped, most likely because they just sent: fetch it now.
                    if (watcher.onStop) watcher.onStop();
                }
            })
            .catch(function () {})
            .then(function () { inFlight = false; });
    }

    /**
     * Watch the open chat. opts: convId() → id, row() → the .ev-typing-row,
     * scroller() → the message list, onStop() → refresh messages.
     * Calling it again (soft navigation) just swaps the options.
     */
    function watch(opts) {
        watcher = opts;
        if (!pollTimer) pollTimer = setInterval(tick, POLL_MS);
        // Cheap DOM-only check so a bubble never lingers into another chat.
        if (!switchTimer) switchTimer = setInterval(function () {
            if (shownConv && currentConv() !== shownConv) hide();
        }, 300);
        tick();
    }

    window.EvTyping = { input: input, stop: stop, reset: reset, watch: watch };
})();
</script>
