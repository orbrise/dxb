<script>
/**
 * WebRTC 1:1 call flow. Signaling piggybacks on the existing chat.{userId}
 * private channel — the callee's browser listens for CallSignal broadcasts
 * (offer/answer/ice/hangup/decline/ringing) relayed by /call/signal.
 *
 * ICE servers:
 *  - Google's public STUN (free, always available)
 *  - Metered Open Relay TURN (free tier: 500 concurrent, 50 GB/mo). Falls
 *    back automatically if STUN alone can't punch through the peer's NAT.
 *
 * State is stored on window.__rtc so it survives Livewire DOM morphs; the
 * whole call UI lives outside the Livewire component root and is manipulated
 * imperatively.
 */
(function () {
    if (window.__rtc) return; // already initialized
    const S = window.__rtc = {
        pollTimer: null,
        pollSeen: new Set(),  // ids of signals we've already processed
        ringOscillators: [],  // active Web Audio oscillators for the incoming ring
        incomingRingInterval: null,
        pc: null,               // RTCPeerConnection
        localStream: null,      // MediaStream (mic + optional camera)
        remoteStream: null,
        callId: null,
        callType: null,         // 'audio' | 'video'
        role: null,             // 'caller' | 'callee'
        peer: null,             // { id, name, avatar }
        conversationId: null,
        pendingIce: [],         // ICE received before remoteDescription is set
        stateTicker: null,
        statsTicker: null,
        startedAt: null,
        connectedAt: null,
        ringOsc: null,          // Web Audio oscillator used as ringback
    };

    // --- ICE servers ---------------------------------------------------
    const iceServers = [
        { urls: 'stun:stun.l.google.com:19302' },
        // Metered Open Relay (free TURN — works when STUN alone fails behind
        // strict NATs). Publicly published credentials.
        {
            urls: 'turn:openrelay.metered.ca:80',
            username: 'openrelayproject',
            credential: 'openrelayproject',
        },
        {
            urls: 'turn:openrelay.metered.ca:443',
            username: 'openrelayproject',
            credential: 'openrelayproject',
        },
        {
            urls: 'turn:openrelay.metered.ca:443?transport=tcp',
            username: 'openrelayproject',
            credential: 'openrelayproject',
        },
    ];

    // --- Public API ----------------------------------------------------
    window.rtcStartCall = async function ({ userId, name, avatar, type, conversationId }) {
        // Self-heal — if a previous call was interrupted (Livewire morph, tab
        // navigation) but S.pc wasn't cleaned up, force-close and start fresh
        // instead of blocking the user with "already in a call" forever.
        if (S.pc) {
            const callVisible = document.getElementById('rtcCall')?.classList.contains('open');
            const state = S.pc.connectionState;
            if (!callVisible || state === 'failed' || state === 'closed' || state === 'disconnected') {
                console.warn('[rtc] stale call state detected, cleaning up before starting new call', { state, callVisible });
                cleanup();
                hideCallUI();
            } else {
                toast('You are already in a call.'); return;
            }
        }
        if (!userId) { toast('No peer selected.'); return; }

        S.callId = uuid();
        S.callType = type;
        S.role = 'caller';
        S.peer = { id: userId, name: name || 'User', avatar: avatar || null };
        S.conversationId = conversationId || null;
        S.startedAt = Date.now();

        showCallUI('Ringing…');
        console.log('[rtc] starting call', { userId, type, callId: S.callId });
        try {
            console.log('[rtc] step: getUserMedia');
            await ensureLocalStream(type);
            console.log('[rtc] step: attachLocalPreview');
            attachLocalPreview();
            console.log('[rtc] step: createPeer');
            S.pc = createPeer();
            console.log('[rtc] step: addTracks', S.localStream.getTracks().length);
            S.localStream.getTracks().forEach(t => S.pc.addTrack(t, S.localStream));
            console.log('[rtc] step: createOffer');
            const offer = await S.pc.createOffer({ offerToReceiveAudio: true, offerToReceiveVideo: type === 'video' });
            console.log('[rtc] step: setLocalDescription');
            await S.pc.setLocalDescription(offer);
            console.log('[rtc] outgoing offer SDP length:', offer.sdp.length,
                'endsWithCRLF:', offer.sdp.endsWith('\r\n'));
            console.log('[rtc] step: postSignal(offer)');
            // Serialize the SessionDescription explicitly (type + sdp) so
            // JSON.stringify doesn't do anything unexpected with the browser's
            // native RTCSessionDescription wrapper.
            await postSignal('offer', { sdp: { type: offer.type, sdp: offer.sdp } });
            console.log('[rtc] offer sent');
            startRingback();
            S.ringTimeout = setTimeout(() => {
                if (S.pc && S.pc.connectionState !== 'connected') {
                    toast('No answer.');
                    endCall(true);
                }
            }, 45000);
        } catch (err) {
            console.error('[rtc] rtcStartCall failed:', err, err?.stack);
            let msg = 'Could not start call.';
            if (err?.name === 'NotAllowedError') msg = 'Camera / mic permission denied.';
            else if (err?.name === 'NotFoundError') msg = 'No microphone / camera detected.';
            else if (err?.name === 'NotReadableError') msg = 'Mic / camera is in use by another app.';
            else if (err?.message) msg = 'Call failed: ' + err.message.slice(0, 140);
            toast(msg);
            endCall(false);
        }
    };

    // --- Signal handler (called by Echo listener in chat.blade.php) ----
    window.rtcHandleSignal = async function (e) {
        // Ignore signals for a different active call. If we have no active
        // call and this is an incoming 'offer', accept it.
        const t = e.type;
        if (S.callId && S.callId !== e.callId && t !== 'offer') return;

        try {
            if (t === 'offer') {
                // Re-offer on an already-active call = ICE restart from the peer.
                // Set as remote description, generate a new answer, send it back.
                if (S.pc && S.callId === e.callId) {
                    try {
                        await S.pc.setRemoteDescription(new RTCSessionDescription({
                            type: e.payload.sdp.type || 'offer',
                            sdp: normalizeSdp(e.payload.sdp.sdp),
                        }));
                        const answer = await S.pc.createAnswer();
                        await S.pc.setLocalDescription(answer);
                        await postSignal('answer', { sdp: { type: answer.type, sdp: answer.sdp } });
                        setState('Reconnecting…');
                    } catch (err) {
                        console.warn('ICE-restart answer failed', err);
                    }
                    return;
                }
                if (S.pc) {
                    // Different call while already busy — reject.
                    const prevCallId = S.callId;
                    S.callId = e.callId;
                    await postSignal('decline', {}, { targetOverride: e.from });
                    S.callId = prevCallId;
                    return;
                }
                S.callId = e.callId;
                S.callType = e.callType;
                S.role = 'callee';
                S.peer = { id: e.from, name: e.fromName || 'Someone', avatar: e.fromAvatar || null };
                await postSignal('ringing', {});
                showIncoming(e);
                // We stash the offer SDP on S for use when the user accepts.
                S._pendingOffer = e.payload.sdp;
                return;
            }

            if (t === 'answer' && S.pc) {
                console.log('[rtc] answer received, sdp_len=', e.payload?.sdp?.sdp?.length);
                await S.pc.setRemoteDescription(new RTCSessionDescription({
                    type: e.payload.sdp.type || 'answer',
                    sdp: normalizeSdp(e.payload.sdp.sdp),
                }));
                await flushPendingIce();
                stopRingback();
                setState('Connecting…');
                return;
            }

            if (t === 'ice') {
                const cand = e.payload.candidate;
                if (!cand) return;
                // Buffer if we don't have a peer connection yet (callee
                // hasn't accepted, or caller hasn't received answer). We
                // apply them via flushPendingIce() after setRemoteDescription.
                if (S.pc && S.pc.remoteDescription && S.pc.remoteDescription.type) {
                    await S.pc.addIceCandidate(new RTCIceCandidate(cand)).catch((err) => {
                        console.warn('[rtc] addIceCandidate failed', err);
                    });
                } else {
                    console.log('[rtc] buffering early ICE, pcReady=', !!S.pc);
                    S.pendingIce.push(cand);
                }
                return;
            }

            if (t === 'decline') {
                toast(S.peer?.name ? `${S.peer.name} declined.` : 'Call declined.');
                endCall(false);
                return;
            }

            if (t === 'hangup') {
                toast('Call ended.');
                endCall(false);
                return;
            }

            if (t === 'ringing') {
                setState('Ringing…');
                return;
            }
        } catch (err) {
            console.error('rtc signal error', err);
        }
    };

    // --- Accept / decline (bound in DOMContentLoaded below) ------------
    async function acceptIncoming() {
        console.log('[rtc] acceptIncoming called', {
            callId: S.callId,
            hasOffer: !!S._pendingOffer,
            offerType: S._pendingOffer?.type,
            offerSdpLen: S._pendingOffer?.sdp?.length,
            callType: S.callType,
        });
        hideIncoming();
        showCallUI('Connecting…');
        let step = 'init';
        try {
            step = 'ensureLocalStream';
            console.log('[rtc] accept step:', step);
            await ensureLocalStream(S.callType);

            step = 'attachLocalPreview';
            console.log('[rtc] accept step:', step);
            attachLocalPreview();

            step = 'createPeer';
            console.log('[rtc] accept step:', step);
            S.pc = createPeer();

            step = 'addTracks';
            console.log('[rtc] accept step:', step, S.localStream.getTracks().length);
            S.localStream.getTracks().forEach(t => S.pc.addTrack(t, S.localStream));

            step = 'setRemoteDescription';
            console.log('[rtc] accept step:', step, {
                type: S._pendingOffer?.type,
                sdp_len: S._pendingOffer?.sdp?.length,
                sdp_endsWithCRLF: S._pendingOffer?.sdp?.endsWith('\r\n'),
                sdp_tail: S._pendingOffer?.sdp?.slice(-60),
            });
            if (!S._pendingOffer || !S._pendingOffer.sdp) {
                throw new Error('missing offer SDP — polling delivered empty payload');
            }
            await S.pc.setRemoteDescription(new RTCSessionDescription({
                type: S._pendingOffer.type || 'offer',
                sdp: normalizeSdp(S._pendingOffer.sdp),
            }));

            step = 'createAnswer';
            console.log('[rtc] accept step:', step);
            const answer = await S.pc.createAnswer();

            step = 'setLocalDescription';
            console.log('[rtc] accept step:', step);
            await S.pc.setLocalDescription(answer);

            step = 'flushPendingIce';
            console.log('[rtc] accept step:', step, 'buffered ICE:', S.pendingIce.length);
            await flushPendingIce();

            step = 'postSignal(answer)';
            console.log('[rtc] accept step:', step, 'answer SDP length:', answer.sdp.length);
            await postSignal('answer', { sdp: { type: answer.type, sdp: answer.sdp } });

            console.log('[rtc] accept COMPLETE');
        } catch (err) {
            console.error('[rtc] accept FAILED at step:', step, err, err?.stack);
            let msg = 'Could not accept call.';
            if (err?.name === 'NotAllowedError') msg = 'Mic permission denied.';
            else if (err?.name === 'NotFoundError') msg = 'No microphone / camera detected.';
            else if (err?.name === 'NotReadableError') msg = 'Mic / camera in use by another app.';
            else if (err?.message) msg = 'Accept failed at ' + step + ': ' + err.message.slice(0, 140);
            toast(msg);
            try { await postSignal('decline', {}); } catch (_) {}
            endCall(false);
        }
    }

    async function declineIncoming() {
        hideIncoming();
        try { await postSignal('decline', {}); } catch (_) {}
        cleanup();
    }

    async function hangup() {
        try { await postSignal('hangup', {}); } catch (_) {}
        endCall(true);
    }

    // --- Peer factory --------------------------------------------------
    function createPeer() {
        const pc = new RTCPeerConnection({ iceServers, iceCandidatePoolSize: 4 });

        pc.onicecandidate = (ev) => {
            if (ev.candidate) postSignal('ice', { candidate: ev.candidate }).catch(() => {});
        };

        pc.ontrack = (ev) => {
            if (!S.remoteStream) {
                S.remoteStream = new MediaStream();
                const el = document.getElementById('rtcRemoteVideo');
                if (el) el.srcObject = S.remoteStream;
            }
            S.remoteStream.addTrack(ev.track);
            // If the remote is sending video, hide the audio-only overlay.
            if (ev.track.kind === 'video') {
                const ao = document.getElementById('rtcAudioOnly');
                if (ao) ao.style.display = 'none';
                const rv = document.getElementById('rtcRemoteVideo');
                if (rv) rv.style.display = 'block';
            }
        };

        pc.onconnectionstatechange = () => {
            const st = pc.connectionState;
            if (st === 'connected') {
                S.connectedAt = S.connectedAt || Date.now();
                setState('In call');
                startTimer();
                startStatsSampling();
                stopRingback();
                clearTimeout(S.ringTimeout);
                // Cancel any pending reconnect timers if we recovered.
                clearTimeout(S.reconnectTimer); S.reconnectTimer = null;
                clearTimeout(S.reconnectHardTimer); S.reconnectHardTimer = null;
            }

            // Transient blip: WebRTC often flips to 'disconnected' for 1-3s
            // on network handoff (WiFi ↔ 4G, VPN switch). Give it grace before
            // treating as fatal — most calls recover on their own within 5s.
            if (st === 'disconnected') {
                setState('Reconnecting…');
                toast('Reconnecting…');
                clearTimeout(S.reconnectTimer);
                S.reconnectTimer = setTimeout(async () => {
                    if (!S.pc || S.pc.connectionState === 'connected') return;
                    // Only the caller side initiates ICE restart to avoid
                    // both peers restarting simultaneously.
                    if (S.role === 'caller') {
                        try {
                            const offer = await S.pc.createOffer({ iceRestart: true });
                            await S.pc.setLocalDescription(offer);
                            await postSignal('offer', { sdp: { type: offer.type, sdp: offer.sdp } });
                        } catch (err) {
                            console.warn('ICE restart failed', err);
                        }
                    }
                    // If not recovered within another 8s → give up.
                    clearTimeout(S.reconnectHardTimer);
                    S.reconnectHardTimer = setTimeout(() => {
                        if (S.pc && S.pc.connectionState !== 'connected') {
                            toast('Connection lost.');
                            endCall(false);
                        }
                    }, 8000);
                }, 6000);
            }

            if (st === 'failed' || st === 'closed') {
                if (st === 'failed') toast('Connection lost.');
                endCall(false);
            }
        };

        return pc;
    }

    async function flushPendingIce() {
        if (!S.pc) return;
        while (S.pendingIce.length) {
            const c = S.pendingIce.shift();
            try { await S.pc.addIceCandidate(new RTCIceCandidate(c)); } catch (_) {}
        }
    }

    // --- Media ---------------------------------------------------------
    async function ensureLocalStream(type) {
        if (S.localStream) return S.localStream;
        S.localStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
            video: type === 'video' ? { width: { ideal: 640 }, height: { ideal: 480 } } : false,
        });
        return S.localStream;
    }

    function attachLocalPreview() {
        const lv = document.getElementById('rtcLocalVideo');
        if (!lv) return;
        if (S.callType === 'video') {
            lv.classList.remove('audio-only');
            lv.srcObject = S.localStream;
        } else {
            lv.classList.add('audio-only');
        }
    }

    // --- Connection quality (poor connection) --------------------------
    function startStatsSampling() {
        if (!S.pc || S.statsTicker) return;
        const quality = document.getElementById('rtcQuality');
        const label = document.getElementById('rtcQualityLabel');
        if (quality) quality.style.display = 'inline-flex';

        S.statsTicker = setInterval(async () => {
            if (!S.pc) return;
            let rttMs = null;
            let lossPct = null;
            try {
                const stats = await S.pc.getStats();
                let inbound = null;
                stats.forEach(r => {
                    if (r.type === 'candidate-pair' && r.state === 'succeeded' && r.currentRoundTripTime != null) {
                        rttMs = r.currentRoundTripTime * 1000;
                    }
                    if (r.type === 'inbound-rtp' && !r.isRemote && (r.kind === 'audio' || r.kind === 'video')) {
                        if (!inbound || r.kind === 'video') inbound = r; // prefer video if present
                    }
                });
                if (inbound && (inbound.packetsLost != null) && (inbound.packetsReceived != null)) {
                    const total = inbound.packetsLost + inbound.packetsReceived;
                    lossPct = total > 0 ? (inbound.packetsLost / total) * 100 : 0;
                }
            } catch (_) {}

            // Thresholds tuned to feel like WhatsApp's indicator.
            let tier = 'good';
            let text = 'Good';
            if ((rttMs != null && rttMs > 700) || (lossPct != null && lossPct > 10)) {
                tier = 'poor'; text = 'Poor connection';
            } else if ((rttMs != null && rttMs > 300) || (lossPct != null && lossPct > 3)) {
                tier = 'fair'; text = 'Fair';
            }
            if (quality) {
                quality.classList.remove('good', 'fair', 'poor');
                quality.classList.add(tier);
            }
            if (label) label.textContent = text;
        }, 2000);
    }

    // --- Timer ---------------------------------------------------------
    function startTimer() {
        const el = document.getElementById('rtcTimer');
        if (!el) return;
        el.style.display = 'inline-block';
        S.stateTicker = setInterval(() => {
            const s = Math.floor((Date.now() - S.connectedAt) / 1000);
            el.textContent = `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
        }, 500);
    }

    // --- Ringback (caller side) via Web Audio ---------------------------
    function startRingback() {
        try {
            const ctx = window.__rtc._audio = window.__rtc._audio || new (window.AudioContext || window.webkitAudioContext)();
            const play = () => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.frequency.value = 440;
                gain.gain.value = 0.15;
                osc.connect(gain).connect(ctx.destination);
                osc.start();
                setTimeout(() => osc.stop(), 400);
                setTimeout(() => {
                    const osc2 = ctx.createOscillator();
                    const g2 = ctx.createGain();
                    osc2.frequency.value = 480;
                    g2.gain.value = 0.15;
                    osc2.connect(g2).connect(ctx.destination);
                    osc2.start();
                    setTimeout(() => osc2.stop(), 400);
                }, 600);
            };
            play();
            S.ringInterval = setInterval(play, 3000);
        } catch (_) { /* Web Audio blocked — silent ringback is fine */ }
    }
    function stopRingback() {
        clearInterval(S.ringInterval); S.ringInterval = null;
    }

    // --- UI helpers ----------------------------------------------------
    function showIncoming(e) {
        const el = document.getElementById('rtcIncoming');
        if (!el) return;
        document.getElementById('rtcIncomingName').textContent = S.peer.name;
        document.getElementById('rtcIncomingType').textContent = S.callType === 'video' ? 'Video' : 'Voice';
        const av = document.getElementById('rtcIncomingAvatar');
        if (S.peer.avatar) {
            av.innerHTML = `<img src="${escapeAttr(S.peer.avatar)}" alt="">`;
        } else {
            av.textContent = (S.peer.name || '?').charAt(0).toUpperCase();
        }
        el.classList.add('open');

        // Try the <audio> element first (if a real ringtone.mp3 is present);
        // if that fails (missing file, autoplay-blocked, etc.) fall back to a
        // synthesized WhatsApp-style two-tone ring via Web Audio.
        startIncomingRing();
    }
    function hideIncoming() {
        document.getElementById('rtcIncoming')?.classList.remove('open');
        stopIncomingRing();
    }

    // --- Incoming ringtone (Web Audio) ---------------------------------
    // Ringtone disabled for now — the incoming banner still shows visually,
    // it just doesn't play audio. Set the flag below to true to re-enable
    // the synthesized WhatsApp-style two-tone ring.
    const RING_ENABLED = false;

    function startIncomingRing() {
        stopIncomingRing(); // ensure clean slate
        if (!RING_ENABLED) return;   // audio disabled — banner only

        // Prefer the <audio> tag if it actually resolves — someone may drop
        // a proper ringtone.mp3 into public/assets/newtheme/ later.
        const audio = document.getElementById('rtcRingtone');
        if (audio && audio.currentSrc) {
            const p = audio.play();
            if (p && typeof p.then === 'function') {
                p.then(() => { S.usingAudioElement = true; })
                 .catch(() => { S.usingAudioElement = false; startSynthLoop(); });
                return;
            }
        }
        startSynthLoop();
    }

    // Schedule the repeating synth ring — the interval is created here,
    // ONCE, so a single stopIncomingRing() call is enough to silence it.
    // (Previous version set the interval inside playSynthRing itself,
    // orphaning a new one on every tick; stopIncomingRing only cleared
    // the newest reference so old intervals kept ringing forever.)
    function startSynthLoop() {
        playSynthRing();
        clearInterval(S.incomingRingInterval);
        S.incomingRingInterval = setInterval(playSynthRing, 3000);
    }

    function playSynthRing() {
        try {
            const ctx = window.__rtc._audio = window.__rtc._audio || new (window.AudioContext || window.webkitAudioContext)();
            // Some browsers start the context in "suspended" state until a
            // user gesture; try to resume so the callee actually hears it.
            if (ctx.state === 'suspended') { ctx.resume().catch(() => {}); }

            const playNote = (freq, startAt, duration) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                // 100ms attack, 100ms release, ~0.22 peak — pleasant, not shrill.
                gain.gain.setValueAtTime(0.0001, startAt);
                gain.gain.exponentialRampToValueAtTime(0.22, startAt + 0.10);
                gain.gain.setValueAtTime(0.22, startAt + duration - 0.10);
                gain.gain.exponentialRampToValueAtTime(0.0001, startAt + duration);
                osc.connect(gain).connect(ctx.destination);
                osc.start(startAt);
                osc.stop(startAt + duration + 0.01);
                S.ringOscillators.push(osc);
            };

            S.ringOscillators = [];
            const now = ctx.currentTime;
            playNote(800,  now,        0.40);
            playNote(1000, now + 0.50, 0.40);
        } catch (err) {
            console.warn('[rtc] synth ringtone failed', err);
        }
    }

    function stopIncomingRing() {
        clearInterval(S.incomingRingInterval);
        S.incomingRingInterval = null;
        if (S.ringOscillators) {
            for (const osc of S.ringOscillators) {
                try { osc.stop(); } catch (_) {}
                try { osc.disconnect(); } catch (_) {}
            }
            S.ringOscillators = [];
        }
        const audio = document.getElementById('rtcRingtone');
        if (audio) { try { audio.pause(); audio.currentTime = 0; } catch (_) {} }
        S.usingAudioElement = false;
    }

    function showCallUI(state) {
        document.getElementById('rtcCall')?.classList.add('open');
        maybeShowSpeakerBtn();
        setState(state);
        document.getElementById('rtcCallName').textContent = S.peer.name;
        const av = document.getElementById('rtcCallAvatar');
        if (S.peer.avatar) av.innerHTML = `<img src="${escapeAttr(S.peer.avatar)}" alt="">`;
        else av.textContent = (S.peer.name || '?').charAt(0).toUpperCase();

        // Audio-only placeholder for voice calls
        const ao = document.getElementById('rtcAudioOnly');
        const rv = document.getElementById('rtcRemoteVideo');
        const videoBtn = document.getElementById('rtcVideoBtn');
        if (S.callType === 'audio') {
            ao.style.display = 'block';
            rv.style.display = 'none';
            if (videoBtn) videoBtn.style.display = 'none';
            document.getElementById('rtcAudioName').textContent = S.peer.name;
            const ba = document.getElementById('rtcAudioAvatar');
            if (S.peer.avatar) ba.innerHTML = `<img src="${escapeAttr(S.peer.avatar)}" alt="">`;
            else ba.textContent = (S.peer.name || '?').charAt(0).toUpperCase();
        } else {
            ao.style.display = 'none';
            rv.style.display = 'block';
            if (videoBtn) videoBtn.style.display = '';
        }
    }
    function hideCallUI() {
        const call = document.getElementById('rtcCall');
        if (call) {
            call.classList.remove('open');
            call.classList.remove('maximized');
        }
        const quality = document.getElementById('rtcQuality');
        if (quality) quality.style.display = 'none';
        const timer = document.getElementById('rtcTimer');
        if (timer) { timer.style.display = 'none'; timer.textContent = '00:00'; }
        // Reset window/speaker button state for the next call.
        const maxBtn = document.getElementById('rtcMaximizeBtn');
        if (maxBtn) {
            maxBtn.title = 'Maximize';
            maxBtn.innerHTML = '<i class="fa fa-expand"></i>';
        }
        const speakerBtn = document.getElementById('rtcSpeakerBtn');
        if (speakerBtn) {
            speakerBtn.style.display = 'none';
            speakerBtn.classList.remove('speaker-on');
        }
        S.currentSinkId = null;
    }
    function setState(txt) {
        const el = document.getElementById('rtcCallState');
        if (el) el.textContent = txt;
    }
    function toast(msg) {
        const el = document.getElementById('rtcToast');
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('open');
        // reflow to restart animation
        void el.offsetWidth;
        el.classList.add('open');
    }

    function endCall(hangupSent) {
        cleanup();
        // Also dismiss the incoming-call banner — otherwise, if the caller
        // hangs up before the callee picks up, the ringing card sits there
        // with an Accept button leading to a dead call.
        hideIncoming();
        setTimeout(() => hideCallUI(), 200);
    }

    function cleanup() {
        stopRingback();
        clearTimeout(S.ringTimeout);
        clearInterval(S.statsTicker); S.statsTicker = null;
        clearInterval(S.stateTicker); S.stateTicker = null;

        if (S.pc) { try { S.pc.close(); } catch (_) {} S.pc = null; }
        if (S.localStream) { S.localStream.getTracks().forEach(t => t.stop()); S.localStream = null; }
        S.remoteStream = null;
        const rv = document.getElementById('rtcRemoteVideo'); if (rv) rv.srcObject = null;
        const lv = document.getElementById('rtcLocalVideo');  if (lv) lv.srcObject = null;
        S.callId = null; S.callType = null; S.peer = null; S.role = null; S.pendingIce = []; S._pendingOffer = null;
    }

    // --- Post signal via Laravel endpoint ------------------------------
    async function postSignal(type, payload, opts = {}) {
        const target = opts.targetOverride || S.peer?.id;
        if (!target) return;
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch('/call/signal', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': token || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                target_user_id: target,
                call_id: S.callId,
                type,
                call_type: S.callType || 'audio',
                payload,
                conversation_id: S.conversationId,
            }),
        });
        if (!res.ok) {
            let body = '';
            try { body = await res.text(); } catch (_) {}
            console.error('[rtc] signal', type, 'HTTP', res.status, body);
            throw new Error('signal failed: ' + res.status + ' ' + body.slice(0, 200));
        }
    }

    // --- Speaker (audio output) toggle ---------------------------------
    // Uses HTMLMediaElement.setSinkId to switch audio output devices.
    // Chrome/Edge/Safari 17+ support this; unsupported browsers hide the
    // button entirely so users don't see a control that does nothing.
    async function toggleSpeaker(btn) {
        const el = document.getElementById('rtcRemoteVideo');
        if (!el || typeof el.setSinkId !== 'function') return;
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const outputs = devices.filter(d => d.kind === 'audiooutput');
            if (outputs.length < 2) return; // only one option — nothing to toggle
            const currentIdx = outputs.findIndex(d => d.deviceId === (S.currentSinkId || 'default'));
            const nextIdx = (currentIdx + 1) % outputs.length;
            const next = outputs[nextIdx];
            await el.setSinkId(next.deviceId);
            S.currentSinkId = next.deviceId;
            // Visual state — "speaker-on" class when we're NOT on the default sink.
            btn.classList.toggle('speaker-on', next.deviceId !== 'default');
        } catch (err) {
            console.warn('[rtc] setSinkId failed', err);
        }
    }

    // Reveal the speaker button when a call starts (and setSinkId works).
    function maybeShowSpeakerBtn() {
        const btn = document.getElementById('rtcSpeakerBtn');
        const el = document.getElementById('rtcRemoteVideo');
        if (!btn || !el) return;
        if (typeof el.setSinkId === 'function') {
            btn.style.display = '';
        }
    }

    // --- Utils ---------------------------------------------------------
    function uuid() {
        return 'call-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
    }

    // Normalize SDP line endings.  WebRTC's SDP parser is strict: every line
    // must end with CRLF (\r\n), including the last one. If MySQL/JSON round-
    // tripping through the DB queue stripped the trailing CRLF (some DB
    // engines trim trailing whitespace on TEXT columns), the parser fails
    // with "Failed to parse SessionDescription" — often halfway through an
    // a=ssrc:... msid:... line, which is what we saw in the wild.
    function normalizeSdp(sdp) {
        if (!sdp) return sdp;
        // Convert any bare \n to \r\n.
        sdp = sdp.replace(/\r?\n/g, '\r\n');
        // Guarantee trailing CRLF.
        if (!sdp.endsWith('\r\n')) sdp += '\r\n';
        return sdp;
    }
    function escapeAttr(s) {
        return String(s).replace(/["<>&]/g, c => ({ '"': '&quot;', '<': '&lt;', '>': '&gt;', '&': '&amp;' }[c]));
    }

    // --- Polling fallback ---------------------------------------------
    // Reverb delivery can be flaky (TLS mismatch, dropped frames, restarts).
    // We ALSO poll the durable signals queue every 1.5s so calls work even
    // when the WebSocket path is broken. Signals are deduped by DB id so
    // getting them twice (once via Reverb, once via poll) is a no-op.
    async function pollPendingSignals() {
        try {
            const res = await fetch('/call/pending', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data = await res.json();
            for (const sig of (data.signals || [])) {
                // The server marked these delivered_at=now(), so subsequent
                // polls won't return them. The Set is just extra insurance
                // against duplicate processing inside this tab.
                const key = `${sig.callId}:${sig.type}:${sig.from}`;
                if (S.pollSeen.has(key)) continue;
                S.pollSeen.add(key);
                if (S.pollSeen.size > 500) {
                    // Bound memory — drop the oldest half.
                    S.pollSeen = new Set(Array.from(S.pollSeen).slice(-250));
                }
                if (typeof window.rtcHandleSignal === 'function') {
                    window.rtcHandleSignal(sig);
                }
            }
        } catch (_) { /* offline, ignore */ }
    }
    function startPolling() {
        if (S.pollTimer) return;
        S.pollTimer = setInterval(pollPendingSignals, 1500);
        // Kick off immediately so a fresh page load doesn't wait 1.5s.
        pollPendingSignals();
    }
    startPolling();

    // --- Bind UI buttons (delegated so they survive Livewire morphs) ---
    document.addEventListener('click', (e) => {
        if (e.target.closest('#rtcAcceptBtn')) { acceptIncoming(); return; }
        if (e.target.closest('#rtcDeclineBtn')) { declineIncoming(); return; }
        if (e.target.closest('#rtcHangupBtn')) { hangup(); return; }

        // Maximize / minimize the floating call window.
        if (e.target.closest('#rtcMaximizeBtn')) {
            const call = document.getElementById('rtcCall');
            const btn = e.target.closest('#rtcMaximizeBtn');
            const isMax = call?.classList.toggle('maximized');
            if (btn) {
                btn.title = isMax ? 'Minimize' : 'Maximize';
                btn.innerHTML = isMax ? '<i class="fa fa-compress"></i>' : '<i class="fa fa-expand"></i>';
            }
            return;
        }

        if (e.target.closest('#rtcMuteBtn')) {
            const btn = e.target.closest('#rtcMuteBtn');
            const track = S.localStream?.getAudioTracks?.()[0];
            if (!track) return;
            track.enabled = !track.enabled;
            btn.classList.toggle('muted', !track.enabled);
            btn.innerHTML = track.enabled ? '<i class="fa fa-microphone"></i>' : '<i class="fa fa-microphone-slash"></i>';
            return;
        }

        // Speaker toggle — cycles through available audio output devices via
        // setSinkId. Useful on phones with headphones/speaker/earpiece
        // combinations; on desktop it switches between "default" and the
        // first non-default output. Silently no-ops on browsers without
        // setSinkId support.
        if (e.target.closest('#rtcSpeakerBtn')) {
            const btn = e.target.closest('#rtcSpeakerBtn');
            toggleSpeaker(btn).catch((err) => console.warn('[rtc] speaker toggle failed', err));
            return;
        }

        if (e.target.closest('#rtcVideoBtn')) {
            const btn = e.target.closest('#rtcVideoBtn');
            const track = S.localStream?.getVideoTracks?.()[0];
            if (!track) return;
            track.enabled = !track.enabled;
            btn.classList.toggle('video-off', !track.enabled);
            btn.innerHTML = track.enabled ? '<i class="fa fa-video"></i>' : '<i class="fa fa-video-slash"></i>';
            return;
        }

        // Chat-header call buttons
        const audioBtn = e.target.closest('[data-rtc-call="audio"]');
        const videoBtn = e.target.closest('[data-rtc-call="video"]');
        if (audioBtn || videoBtn) {
            const btn = audioBtn || videoBtn;
            window.rtcStartCall({
                userId: parseInt(btn.dataset.peerId, 10),
                name: btn.dataset.peerName,
                avatar: btn.dataset.peerAvatar || null,
                type: audioBtn ? 'audio' : 'video',
                conversationId: parseInt(btn.dataset.conversationId, 10) || null,
            });
        }
    });
})();
</script>
