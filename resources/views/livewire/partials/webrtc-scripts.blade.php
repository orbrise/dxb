<script data-navigate-once>
// Diagnostic — if this line prints more than once per browser session,
// data-navigate-once isn't taking effect and window.__rtc state is being
// wiped on every wire:navigate.
console.log('[rtc] script tag executed. existing __rtc:', typeof window.__rtc, window.__rtc ? 'HAS state' : 'no state');
/**
 * WebRTC 1:1 call flow. Simple phone-call UI (avatar / mute / hangup / timer),
 * NOT a meeting-style embed. Media relays through our own coturn at
 * turn.evoory.com (66.29.136.23) — see /rtc/turn for the HMAC creds endpoint.
 *
 * Signaling piggybacks on the /call/signal + /call/pending polling channel:
 *   - offer:   caller → callee (SDP + call metadata)
 *   - answer:  callee → caller (SDP)
 *   - ice:     both directions, one signal per candidate
 *   - ringing: callee → caller (visual "ringing…" state)
 *   - decline: callee → caller
 *   - hangup:  either → other
 *
 * State lives on window.__rtc so it survives Livewire DOM morphs. The whole
 * call UI is rendered OUTSIDE the Livewire component root so re-renders
 * (chat page's 3s refreshChat poll, message send re-renders) can't strip the
 * .open class off the call container or blow away the video srcObject.
 */
(function () {
    if (window.__rtc) return; // already initialized
    const S = window.__rtc = {
        pollTimer: null,
        pollSeen: new Set(),
        dismissedCallIds: new Set(),
        ringOscillators: [],
        incomingRingInterval: null,
        pc: null,               // RTCPeerConnection
        localStream: null,      // getUserMedia stream
        remoteStream: null,     // combined stream (for callers that reference it)
        remoteAudioStream: null,
        remoteVideoStream: null,
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
        iceServers: null,       // populated by fetchIceServers() on first call
    };

    // Minimal fallback if /rtc/turn fetch fails — same-network calls still
    // work via host candidates; cross-NAT calls will fail cleanly rather
    // than hanging until the browser's ICE timeout.
    const FALLBACK_ICE_SERVERS = [
        { urls: 'stun:stun.l.google.com:19302' },
    ];

    async function fetchIceServers() {
        if (S.iceServers) return S.iceServers;
        try {
            const csrf = document.querySelector('meta[name=csrf-token]')?.content;
            const res = await fetch('/rtc/turn', {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
            });
            if (!res.ok) throw new Error('turn endpoint returned ' + res.status);
            const data = await res.json();
            if (!Array.isArray(data.iceServers) || !data.iceServers.length) {
                throw new Error('turn endpoint returned no iceServers');
            }
            S.iceServers = data.iceServers;
            S.iceSource = data.source || 'unknown';
            console.log('[rtc] iceServers loaded, source:', data.source, 'count:', data.iceServers.length);
            return S.iceServers;
        } catch (err) {
            console.warn('[rtc] fetchIceServers failed, using STUN-only fallback:', err);
            S.iceServers = FALLBACK_ICE_SERVERS;
            return S.iceServers;
        }
    }

    // --- Public API — start an outbound call ---------------------------
    window.rtcStartCall = async function ({ userId, name, avatar, type, conversationId }) {
        // Self-heal — if a previous call left S.pc lingering after a Livewire
        // morph or tab nav, force-close and start fresh rather than blocking
        // the user with "already in a call" forever.
        if (S.pc) {
            const callVisible = document.getElementById('rtcCall')?.classList.contains('open');
            const state = S.pc.connectionState;
            if (!callVisible || state === 'failed' || state === 'closed' || state === 'disconnected') {
                console.warn('[rtc] stale call state — cleaning up before new call', { state, callVisible });
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
            await ensureLocalStream(type);
            attachLocalPreview();
            await fetchIceServers();
            S.pc = createPeer();
            S.localStream.getTracks().forEach(t => S.pc.addTrack(t, S.localStream));

            const offer = await S.pc.createOffer({
                offerToReceiveAudio: true,
                offerToReceiveVideo: type === 'video',
            });
            await S.pc.setLocalDescription(offer);
            await postSignal('offer', { sdp: { type: offer.type, sdp: sanitizeOutgoingSdp(offer.sdp) } });

            startRingback();
            S.ringTimeout = setTimeout(() => {
                if (S.pc && S.pc.connectionState !== 'connected') {
                    toast('No answer.');
                    endCall(true);
                }
            }, 45000);
        } catch (err) {
            console.error('[rtc] rtcStartCall failed:', err);
            let msg = 'Could not start call.';
            if (err?.name === 'NotAllowedError') msg = 'Camera / mic permission denied.';
            else if (err?.name === 'NotFoundError') msg = 'No microphone / camera detected.';
            else if (err?.name === 'NotReadableError') msg = 'Mic / camera is in use by another app.';
            else if (err?.message) msg = 'Call failed: ' + err.message.slice(0, 140);
            toast(msg);
            endCall(false);
        }
    };

    // --- Signal handler (delivered by polling /call/pending) ----------
    window.rtcHandleSignal = async function (e) {
        const t = e.type;
        if (S.callId && S.callId !== e.callId && t !== 'offer') return;

        try {
            if (t === 'offer') {
                // Suppress re-ring for calls the user already dismissed
                if (S.dismissedCallIds.has(e.callId)) {
                    const prev = S.callId;
                    S.callId = e.callId;
                    try { await postSignal('decline', {}, { targetOverride: e.from }); } catch (_) {}
                    S.callId = prev;
                    return;
                }
                // ICE-restart offer arriving on an active call
                if (S.pc && S.callId === e.callId) {
                    try {
                        await S.pc.setRemoteDescription(new RTCSessionDescription({
                            type: e.payload.sdp.type || 'offer',
                            sdp: sanitizeIncomingSdp(normalizeSdp(e.payload.sdp.sdp)),
                        }));
                        const answer = await S.pc.createAnswer();
                        await S.pc.setLocalDescription(answer);
                        await postSignal('answer', { sdp: { type: answer.type, sdp: sanitizeOutgoingSdp(answer.sdp) } });
                        setState('Reconnecting…');
                    } catch (err) {
                        console.warn('[rtc] ICE-restart answer failed', err);
                    }
                    return;
                }
                // Busy — reject the new call
                if (S.pc) {
                    const prev = S.callId;
                    S.callId = e.callId;
                    await postSignal('decline', {}, { targetOverride: e.from });
                    S.callId = prev;
                    return;
                }
                // Fresh incoming — show the incoming banner
                S.callId = e.callId;
                S.callType = e.callType;
                S.role = 'callee';
                S.peer = { id: e.from, name: e.fromName || 'Someone', avatar: e.fromAvatar || null };
                await postSignal('ringing', {});
                showIncoming(e);
                S._pendingOffer = e.payload.sdp;
                // Pre-warm mic (and camera for video calls) in parallel with
                // the ringtone. getUserMedia can eat 300-700ms on first call
                // while the browser wakes the hardware; kicking it off now
                // means acceptIncoming() gets a ready stream instantly on
                // click. WhatsApp / Zoom / Teams all do this. If the user
                // declines, cleanup() stops the tracks.
                ensureLocalStream(e.callType).catch(err => {
                    // Don't tear down the incoming UI just because mic
                    // access failed — user might have blocked permission
                    // and we want acceptIncoming() to surface the real
                    // error message.
                    console.warn('[rtc] prewarm getUserMedia failed', err);
                });
                return;
            }

            if (t === 'answer' && S.pc) {
                await S.pc.setRemoteDescription(new RTCSessionDescription({
                    type: e.payload.sdp.type || 'answer',
                    sdp: sanitizeIncomingSdp(normalizeSdp(e.payload.sdp.sdp)),
                }));
                await flushPendingIce();
                stopRingback();
                setState('Connecting…');
                return;
            }

            if (t === 'ice') {
                const cand = e.payload.candidate;
                if (!cand) return;
                // With TURN: reject peer candidates with private IPs —
                // coturn would deny CreatePermission for them anyway
                // (RFC1918), and accepting them just wastes ICE budget on
                // checks that can't succeed.
                // Without TURN (local dev): we NEED private IPs — the peer
                // is on the same LAN and host candidates carry LAN addrs.
                if (S.hasTurn) {
                    const candStr = cand.candidate || '';
                    const parts = candStr.split(' ');
                    const ip = parts[4] || '';
                    const typIdx = parts.indexOf('typ');
                    const typ = typIdx >= 0 ? parts[typIdx + 1] : '';
                    if (isPrivateIp(ip)) {
                        console.log('[rtc] dropped remote candidate (private ip)', { typ, ip });
                        return;
                    }
                }
                if (S.pc && S.pc.remoteDescription && S.pc.remoteDescription.type) {
                    await S.pc.addIceCandidate(new RTCIceCandidate(cand)).catch(() => {});
                } else {
                    S.pendingIce.push(cand);
                }
                return;
            }

            if (t === 'decline') {
                toast(S.peer?.name ? `${S.peer.name} declined.` : 'Call declined.');
                endCall(false);
                return;
            }
            if (t === 'hangup') { toast('Call ended.'); endCall(false); return; }
            if (t === 'ringing') { setState('Ringing…'); return; }
        } catch (err) {
            console.error('[rtc] signal error', err);
        }
    };

    // --- Accept incoming call -----------------------------------------
    async function acceptIncoming() {
        hideIncoming();
        showCallUI('Connecting…');
        try {
            await ensureLocalStream(S.callType);
            attachLocalPreview();
            await fetchIceServers();
            S.pc = createPeer();
            S.localStream.getTracks().forEach(t => S.pc.addTrack(t, S.localStream));

            if (!S._pendingOffer || !S._pendingOffer.sdp) {
                throw new Error('missing offer SDP');
            }
            await S.pc.setRemoteDescription(new RTCSessionDescription({
                type: S._pendingOffer.type || 'offer',
                sdp: sanitizeIncomingSdp(normalizeSdp(S._pendingOffer.sdp)),
            }));
            const answer = await S.pc.createAnswer();
            await S.pc.setLocalDescription(answer);
            await flushPendingIce();
            await postSignal('answer', { sdp: { type: answer.type, sdp: sanitizeOutgoingSdp(answer.sdp) } });
        } catch (err) {
            console.error('[rtc] accept failed', err);
            let msg = 'Could not accept call.';
            if (err?.name === 'NotAllowedError') msg = 'Mic permission denied.';
            else if (err?.name === 'NotFoundError') msg = 'No microphone / camera detected.';
            else if (err?.name === 'NotReadableError') msg = 'Mic / camera in use by another app.';
            else if (err?.message) msg = 'Accept failed: ' + err.message.slice(0, 140);
            toast(msg);
            try { await postSignal('decline', {}); } catch (_) {}
            endCall(false);
        }
    }

    function rememberDismissed(callId) {
        if (!callId) return;
        S.dismissedCallIds.add(callId);
        if (S.dismissedCallIds.size > 400) {
            S.dismissedCallIds = new Set(Array.from(S.dismissedCallIds).slice(-200));
        }
    }
    async function declineIncoming() {
        rememberDismissed(S.callId);
        hideIncoming();
        try { await postSignal('decline', {}); } catch (_) {}
        cleanup();
    }
    async function hangup() {
        rememberDismissed(S.callId);
        try { await postSignal('hangup', {}); } catch (_) {}
        endCall(true);
    }

    // --- Peer factory -------------------------------------------------
    function createPeer() {
        // Prefer iceTransportPolicy='relay' when we have TURN — browsers
        // otherwise try host/srflx pairs first and can give up before
        // attempting the relay pair on cross-country calls. Confirmed via
        // coturn logs: with 'all', peer_usage rp/sp counters stayed at 0
        // (relay never engaged). With 'relay', the browser immediately
        // does CREATE_PERMISSION + CHANNEL_BIND on its TURN allocation and
        // media flows. Same pattern WhatsApp/Zoom use for reliability.
        //
        // BUT when TURN isn't configured (local dev, source='stun-only'),
        // forcing relay gives the browser nothing to allocate on — ICE
        // gathering completes with zero candidates and the call dies before
        // it even offers. Fall back to 'all' in that case so at least
        // host/srflx pairs get tried (works for same-machine / same-LAN
        // local testing; cross-NAT still fails but at least it fails
        // AFTER attempting rather than never getting off the ground).
        const hasTurn = Array.isArray(S.iceServers) && S.iceServers.some(s => {
            const urls = Array.isArray(s.urls) ? s.urls : [s.urls];
            return urls.some(u => typeof u === 'string' && /^turns?:/i.test(u));
        });
        S.hasTurn = hasTurn;
        const policy = hasTurn ? 'relay' : 'all';
        const pc = new RTCPeerConnection({
            iceServers: S.iceServers || FALLBACK_ICE_SERVERS,
            iceTransportPolicy: policy,
            bundlePolicy: 'max-bundle',
            rtcpMuxPolicy: 'require',
        });
        console.log('[rtc] peer created, transport:', policy, hasTurn ? '(forced)' : '(no TURN → fallback)');

        pc.onicecandidate = (ev) => {
            if (ev.candidate) {
                const c = ev.candidate.candidate || '';
                const typ = c.match(/typ (\S+)/)?.[1] || '?';
                const proto = c.match(/(udp|tcp)\s/i)?.[1] || '?';
                const ip = c.split(' ')[4] || '';
                // Only apply relay-only / private-IP filtering when TURN is
                // actually in play. Without TURN (local dev), we NEED host
                // candidates with private IPs — filtering them out would
                // leave the browser with zero candidates to trickle.
                if (S.hasTurn) {
                    const isPriv =
                        /^10\./.test(ip) ||
                        /^192\.168\./.test(ip) ||
                        /^172\.(1[6-9]|2\d|3[01])\./.test(ip) ||
                        /^169\.254\./.test(ip) ||
                        /^127\./.test(ip) ||
                        /^fe80:/i.test(ip) || /^fc/i.test(ip) || /^fd/i.test(ip);
                    if (typ !== 'relay' || isPriv) {
                        console.log('[rtc] dropped local candidate', { typ, proto, ip });
                        return;
                    }
                }
                console.log('[rtc] local candidate', { typ, proto, ip });
                postSignal('ice', { candidate: ev.candidate }).catch(() => {});
            } else {
                console.log('[rtc] local ICE gathering complete');
            }
        };

        // Split remote tracks by kind. Mobile browsers refuse to autoplay
        // audio through a <video> tag when the video track is absent
        // (voice-only calls), leaving the remote silent. Routing audio to a
        // dedicated <audio> element fixes that.
        pc.ontrack = (ev) => {
            const rv = document.getElementById('rtcRemoteVideo');
            const ra = document.getElementById('rtcRemoteAudio');

            if (ev.track.kind === 'audio') {
                if (!S.remoteAudioStream) {
                    S.remoteAudioStream = new MediaStream();
                    if (ra) ra.srcObject = S.remoteAudioStream;
                }
                S.remoteAudioStream.addTrack(ev.track);
                if (ra && ra.paused) ra.play().catch(() => {});
            } else if (ev.track.kind === 'video') {
                if (!S.remoteVideoStream) {
                    S.remoteVideoStream = new MediaStream();
                    if (rv) rv.srcObject = S.remoteVideoStream;
                }
                S.remoteVideoStream.addTrack(ev.track);
                const ao = document.getElementById('rtcAudioOnly');
                if (ao) ao.style.display = 'none';
                if (rv) rv.style.display = 'block';
                if (rv && rv.paused) rv.play().catch(() => {});
            }

            if (!S.remoteStream) S.remoteStream = new MediaStream();
            S.remoteStream.addTrack(ev.track);
        };

        pc.oniceconnectionstatechange = () => {
            console.log('[rtc] iceConnectionState →', pc.iceConnectionState);
        };
        pc.onicegatheringstatechange = () => {
            console.log('[rtc] iceGatheringState →', pc.iceGatheringState);
        };

        pc.onconnectionstatechange = () => {
            const st = pc.connectionState;
            console.log('[rtc] connectionState →', st);
            // On any terminal-ish state, dump the selected candidate pair
            // and DTLS/ICE transport state so we can diagnose one-way media
            // or failed handshakes without needing chrome://webrtc-internals.
            if (st === 'connected' || st === 'failed' || st === 'disconnected') {
                pc.getStats().then(stats => {
                    let pair = null, localCand = null, remoteCand = null;
                    stats.forEach(r => {
                        if (r.type === 'candidate-pair' && (r.selected || r.nominated) && r.state === 'succeeded') pair = r;
                    });
                    if (!pair) stats.forEach(r => {
                        if (r.type === 'candidate-pair' && r.state === 'succeeded' && r.bytesReceived > 0) pair = r;
                    });
                    if (pair) {
                        stats.forEach(r => {
                            if (r.id === pair.localCandidateId) localCand = r;
                            if (r.id === pair.remoteCandidateId) remoteCand = r;
                        });
                    }
                    console.log('[rtc] state=' + st + ' pair', {
                        localType: localCand?.candidateType,
                        localProto: localCand?.protocol,
                        localAddr: localCand?.address || localCand?.ip,
                        remoteType: remoteCand?.candidateType,
                        remoteProto: remoteCand?.protocol,
                        remoteAddr: remoteCand?.address || remoteCand?.ip,
                        bytesSent: pair?.bytesSent,
                        bytesReceived: pair?.bytesReceived,
                        rtt: pair?.currentRoundTripTime,
                    });
                }).catch(() => {});
            }
            if (st === 'connected') {
                S.connectedAt = S.connectedAt || Date.now();
                setState('In call');
                startTimer();
                startStatsSampling();
                stopRingback();
                clearTimeout(S.ringTimeout);
                clearTimeout(S.reconnectTimer); S.reconnectTimer = null;
                clearTimeout(S.reconnectHardTimer); S.reconnectHardTimer = null;
            }
            // disconnected = transient blip; failed = ICE gave up. Cross-
            // country relay calls (peer A → TURN in US → peer B in Asia)
            // can briefly disconnect during DTLS handshake or media
            // stabilization; give them time to recover before restarting.
            if (st === 'disconnected' || st === 'failed') {
                setState('Reconnecting…');
                clearTimeout(S.reconnectTimer);
                // Longer grace for 'disconnected' — cross-country calls
                // often recover on their own within 10-15 seconds.
                const graceMs = (st === 'failed') ? 2000 : 12000;
                S.reconnectTimer = setTimeout(async () => {
                    if (!S.pc || S.pc.connectionState === 'connected') return;
                    if (S.role === 'caller') {
                        try {
                            const offer = await S.pc.createOffer({ iceRestart: true });
                            await S.pc.setLocalDescription(offer);
                            await postSignal('offer', { sdp: { type: offer.type, sdp: sanitizeOutgoingSdp(offer.sdp) } });
                        } catch (err) {
                            console.warn('[rtc] ICE restart failed', err);
                        }
                    }
                    clearTimeout(S.reconnectHardTimer);
                    S.reconnectHardTimer = setTimeout(() => {
                        if (S.pc && S.pc.connectionState !== 'connected') {
                            toast('Connection lost.');
                            endCall(false);
                        }
                    }, 15000);
                }, graceMs);
            }
            if (st === 'closed') endCall(false);
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

    // --- Media --------------------------------------------------------
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

    // --- Connection quality indicator ---------------------------------
    function startStatsSampling() {
        if (!S.pc || S.statsTicker) return;
        const quality = document.getElementById('rtcQuality');
        const label = document.getElementById('rtcQualityLabel');
        if (quality) quality.style.display = 'inline-flex';

        S.statsTicker = setInterval(async () => {
            if (!S.pc) return;
            let rttMs = null, lossPct = null;
            try {
                const stats = await S.pc.getStats();
                let inbound = null;
                stats.forEach(r => {
                    if (r.type === 'candidate-pair' && r.state === 'succeeded' && r.currentRoundTripTime != null) {
                        rttMs = r.currentRoundTripTime * 1000;
                    }
                    if (r.type === 'inbound-rtp' && !r.isRemote && (r.kind === 'audio' || r.kind === 'video')) {
                        if (!inbound || r.kind === 'video') inbound = r;
                    }
                });
                if (inbound && (inbound.packetsLost != null) && (inbound.packetsReceived != null)) {
                    const total = inbound.packetsLost + inbound.packetsReceived;
                    lossPct = total > 0 ? (inbound.packetsLost / total) * 100 : 0;
                }
            } catch (_) {}

            let tier = 'good', text = 'Good';
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

    // --- Timer --------------------------------------------------------
    function startTimer() {
        const el = document.getElementById('rtcTimer');
        if (!el) return;
        el.style.display = 'inline-block';
        S.stateTicker = setInterval(() => {
            const s = Math.floor((Date.now() - S.connectedAt) / 1000);
            el.textContent = `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
        }, 500);
    }

    // --- Ringback (caller side) --------------------------------------
    function startRingback() {
        try {
            const ctx = window.__rtc._audio = window.__rtc._audio || new (window.AudioContext || window.webkitAudioContext)();
            const play = () => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.frequency.value = 440; gain.gain.value = 0.15;
                osc.connect(gain).connect(ctx.destination);
                osc.start();
                setTimeout(() => osc.stop(), 400);
            };
            play();
            S.ringInterval = setInterval(play, 3000);
        } catch (_) {}
    }
    function stopRingback() {
        clearInterval(S.ringInterval); S.ringInterval = null;
    }

    // --- Incoming ringtone -------------------------------------------
    // Preferred: play the admin-uploaded ringtone file (WhatsApp-style loop)
    // so users know a call is arriving even if the browser tab is minimized.
    // Set from the admin panel (App Settings → Incoming Call Ringtone) and
    // injected from Blade — see below.
    // Fallback: synth two-note ring via Web Audio if no file is configured
    // OR the browser blocks autoplay for the file (rare — tab has already
    // received a user gesture by the time a call arrives).
    const RING_ENABLED = true;
    const RINGTONE_URL = @json($setting?->call_ringtone_path ? smart_asset($setting->call_ringtone_path) : null);

    function startIncomingRing() {
        stopIncomingRing();
        if (!RING_ENABLED) return;
        if (RINGTONE_URL) {
            startFileRing().catch(() => startSynthLoop());
        } else {
            startSynthLoop();
        }
    }
    async function startFileRing() {
        // Reuse a single HTMLAudioElement across the session so we don't
        // leak elements on repeated calls. Looping playback continues when
        // the tab is minimized (browsers only throttle timers, not audio).
        let a = S.ringAudio;
        if (!a) {
            a = new Audio(RINGTONE_URL);
            a.loop = true;
            a.preload = 'auto';
            a.volume = 0.9;
            S.ringAudio = a;
        }
        // Rewind in case a previous call left it mid-track.
        try { a.currentTime = 0; } catch (_) {}
        // .play() returns a Promise that rejects if autoplay is blocked.
        // Rethrow so the caller falls back to the synth ring.
        await a.play();
    }
    function startSynthLoop() {
        playSynthRing();
        clearInterval(S.incomingRingInterval);
        S.incomingRingInterval = setInterval(playSynthRing, 3000);
    }
    function playSynthRing() {
        try {
            const ctx = window.__rtc._audio = window.__rtc._audio || new (window.AudioContext || window.webkitAudioContext)();
            if (ctx.state === 'suspended') ctx.resume().catch(() => {});
            const playNote = (freq, startAt, duration) => {
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
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
        } catch (_) {}
    }
    function stopIncomingRing() {
        clearInterval(S.incomingRingInterval);
        S.incomingRingInterval = null;
        if (S.ringAudio) {
            try { S.ringAudio.pause(); } catch (_) {}
            try { S.ringAudio.currentTime = 0; } catch (_) {}
        }
        if (S.ringOscillators) {
            for (const osc of S.ringOscillators) {
                try { osc.stop(); } catch (_) {}
                try { osc.disconnect(); } catch (_) {}
            }
            S.ringOscillators = [];
        }
    }

    // --- UI helpers ---------------------------------------------------
    function showIncoming(e) {
        const el = document.getElementById('rtcIncoming');
        if (!el) return;
        const nm = document.getElementById('rtcIncomingName');
        if (nm) nm.textContent = S.peer.name;
        const tp = document.getElementById('rtcIncomingType');
        if (tp) tp.textContent = S.callType === 'video' ? 'Video' : 'Voice';
        const av = document.getElementById('rtcIncomingAvatar');
        if (av) {
            if (S.peer.avatar) av.innerHTML = `<img src="${escapeAttr(S.peer.avatar)}" alt="">`;
            else av.textContent = (S.peer.name || '?').charAt(0).toUpperCase();
        }
        el.classList.add('open');
        startIncomingRing();
    }
    function hideIncoming() {
        document.getElementById('rtcIncoming')?.classList.remove('open');
        stopIncomingRing();
    }
    function showCallUI(state) {
        document.getElementById('rtcCall')?.classList.add('open');
        maybeShowSpeakerBtn();
        setState(state);
        const nameEl = document.getElementById('rtcCallName');
        if (nameEl) nameEl.textContent = S.peer.name;
        const av = document.getElementById('rtcCallAvatar');
        if (av) {
            if (S.peer.avatar) av.innerHTML = `<img src="${escapeAttr(S.peer.avatar)}" alt="">`;
            else av.textContent = (S.peer.name || '?').charAt(0).toUpperCase();
        }
        const ao = document.getElementById('rtcAudioOnly');
        const rv = document.getElementById('rtcRemoteVideo');
        const videoBtn = document.getElementById('rtcVideoBtn');
        if (S.callType === 'audio') {
            if (ao) ao.style.display = 'block';
            if (rv) rv.style.display = 'none';
            if (videoBtn) videoBtn.style.display = 'none';
            const audioName = document.getElementById('rtcAudioName');
            if (audioName) audioName.textContent = S.peer.name;
            const ba = document.getElementById('rtcAudioAvatar');
            if (ba) {
                if (S.peer.avatar) ba.innerHTML = `<img src="${escapeAttr(S.peer.avatar)}" alt="">`;
                else ba.textContent = (S.peer.name || '?').charAt(0).toUpperCase();
            }
        } else {
            if (ao) ao.style.display = 'none';
            if (rv) rv.style.display = 'block';
            if (videoBtn) videoBtn.style.display = '';
        }
    }
    function hideCallUI() {
        const call = document.getElementById('rtcCall');
        if (call) { call.classList.remove('open'); call.classList.remove('maximized'); }
        const quality = document.getElementById('rtcQuality');
        if (quality) quality.style.display = 'none';
        const timer = document.getElementById('rtcTimer');
        if (timer) { timer.style.display = 'none'; timer.textContent = '00:00'; }
        const maxBtn = document.getElementById('rtcMaximizeBtn');
        if (maxBtn) { maxBtn.title = 'Maximize'; maxBtn.innerHTML = '<i class="fa fa-expand"></i>'; }
        const speakerBtn = document.getElementById('rtcSpeakerBtn');
        if (speakerBtn) { speakerBtn.style.display = 'none'; speakerBtn.classList.remove('speaker-on'); }
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
        void el.offsetWidth;
        el.classList.add('open');
    }

    function endCall() {
        rememberDismissed(S.callId);
        cleanup();
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
        S.remoteAudioStream = null;
        S.remoteVideoStream = null;
        const rv = document.getElementById('rtcRemoteVideo'); if (rv) rv.srcObject = null;
        const lv = document.getElementById('rtcLocalVideo');  if (lv) lv.srcObject = null;
        const ra = document.getElementById('rtcRemoteAudio'); if (ra) ra.srcObject = null;
        S.callId = null; S.callType = null; S.peer = null; S.role = null;
        S.pendingIce = []; S._pendingOffer = null;
        S.startedAt = null; S.connectedAt = null;
    }

    // --- Post signal via Laravel endpoint -----------------------------
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
            throw new Error('signal ' + type + ' failed: ' + res.status + ' ' + body.slice(0, 200));
        }
    }

    // --- Speaker toggle (setSinkId) ----------------------------------
    async function toggleSpeaker(btn) {
        const el = document.getElementById('rtcRemoteVideo');
        if (!el || typeof el.setSinkId !== 'function') return;
        try {
            const devices = await navigator.mediaDevices.enumerateDevices();
            const outputs = devices.filter(d => d.kind === 'audiooutput');
            if (outputs.length < 2) return;
            const currentIdx = outputs.findIndex(d => d.deviceId === (S.currentSinkId || 'default'));
            const nextIdx = (currentIdx + 1) % outputs.length;
            const next = outputs[nextIdx];
            await el.setSinkId(next.deviceId);
            S.currentSinkId = next.deviceId;
            btn.classList.toggle('speaker-on', next.deviceId !== 'default');
        } catch (err) {
            console.warn('[rtc] setSinkId failed', err);
        }
    }
    function maybeShowSpeakerBtn() {
        const btn = document.getElementById('rtcSpeakerBtn');
        const el = document.getElementById('rtcRemoteVideo');
        if (!btn || !el) return;
        if (typeof el.setSinkId === 'function') btn.style.display = '';
    }

    // --- Utils --------------------------------------------------------
    function uuid() {
        return 'call-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
    }
    function normalizeSdp(sdp) {
        if (!sdp) return sdp;
        sdp = sdp.replace(/\r?\n/g, '\r\n');
        if (!sdp.endsWith('\r\n')) sdp += '\r\n';
        return sdp;
    }

    // Public IP of our TURN relay (turn.evoory.com → 66.29.136.23). Used as
    // the replacement value for c=/o=/rtcp addresses that would otherwise
    // leak a private IP. Since we force relay-only ICE, all real media flows
    // through this IP anyway — using it as the SDP "default" address avoids
    // Firefox getting confused by 0.0.0.0 in c= (Chrome tolerates it, Firefox
    // is stricter and sometimes fails to open the reverse path).
    const RELAY_PUBLIC_IP = '66.29.136.23';

    const isPrivateIp = (ip) => {
        if (!ip || ip === '0.0.0.0') return false;
        if (/^10\./.test(ip)) return true;
        if (/^192\.168\./.test(ip)) return true;
        if (/^172\.(1[6-9]|2\d|3[01])\./.test(ip)) return true;
        if (/^169\.254\./.test(ip)) return true;
        if (/^127\./.test(ip)) return true;
        if (/^fe80:/i.test(ip)) return true;
        if (/^fc/i.test(ip) || /^fd/i.test(ip)) return true;
        return false;
    };

    // Strip RFC1918/private-IP references from an SDP so the peer's TURN
    // allocation doesn't try to CreatePermission for a LAN address (e.g.
    // VirtualBox host-only 192.168.56.1). coturn denies RFC1918 targets,
    // and the failed permission means the peer never opens the return
    // media path → DTLS stalls → call fails.
    //
    // Applied to BOTH directions:
    //   - Outgoing (offer/answer we send) — strips OUR private IPs so the
    //     peer doesn't try to relay to us at a bogus address.
    //   - Incoming (offer/answer we receive) — strips the PEER's private
    //     IPs so WE don't try to relay to them at a bogus address.
    //
    // For `direction: 'out'` we also strip any a=candidate lines that
    // aren't typ=relay (iceTransportPolicy='relay' should already prevent
    // gathering them, but Chrome has been observed leaking on VirtualBox /
    // VPN adapters).
    //
    // Private IPs in c=/o=/a=rtcp are rewritten to the TURN relay's public
    // IP (66.29.136.23), not 0.0.0.0. Firefox is stricter than Chrome about
    // 0.0.0.0 as an SDP default and can fail to open the reverse ICE path
    // when it sees it. Using the relay IP is safe because all our media
    // flows through it anyway under relay-only policy.
    function sanitizeSdp(sdp, direction) {
        if (!sdp) return sdp;
        // No TURN in play → skip sanitize entirely. Rewriting c=/o=/rtcp
        // to the (nonexistent) relay IP would break same-LAN calls in
        // local dev, and stripping non-relay candidates would leave the
        // SDP with none. Sanitize is only correct when TURN is authoritative.
        if (!S.hasTurn) return sdp;
        const stripNonRelay = direction === 'out';

        const lines = sdp.split(/\r?\n/);
        const out = [];
        let changed = 0;
        for (const line of lines) {
            // a=candidate: — only strip on outgoing (we can't rewrite peer's list)
            if (line.startsWith('a=candidate:')) {
                const parts = line.split(' ');
                const ip = parts[4];
                const typIdx = parts.indexOf('typ');
                const typ = typIdx >= 0 ? parts[typIdx + 1] : '';
                if (isPrivateIp(ip) || (stripNonRelay && typ !== 'relay')) {
                    changed++;
                    continue;
                }
            }
            // c=IN IP4/IP6 <addr>
            if (line.startsWith('c=IN IP4 ') || line.startsWith('c=IN IP6 ')) {
                const addr = line.split(' ')[2];
                if (isPrivateIp(addr)) {
                    out.push('c=IN IP4 ' + RELAY_PUBLIC_IP);
                    changed++;
                    continue;
                }
            }
            // o=- <sess-id> <sess-ver> IN IP4 <addr>
            if (line.startsWith('o=')) {
                const parts = line.split(' ');
                if (parts.length >= 6 && (parts[3] === 'IN') && isPrivateIp(parts[5])) {
                    parts[4] = 'IP4';
                    parts[5] = RELAY_PUBLIC_IP;
                    out.push(parts.join(' '));
                    changed++;
                    continue;
                }
            }
            // a=rtcp:9 IN IP4 <addr>
            if (line.startsWith('a=rtcp:')) {
                const parts = line.split(' ');
                if (parts.length >= 4 && parts[1] === 'IN' && isPrivateIp(parts[3])) {
                    parts[2] = 'IP4';
                    parts[3] = RELAY_PUBLIC_IP;
                    out.push(parts.join(' '));
                    changed++;
                    continue;
                }
            }
            out.push(line);
        }
        if (changed) console.log('[rtc] sanitizeSdp[' + direction + '] rewrote/stripped', changed, 'lines');
        return out.join('\r\n');
    }

    // Backwards-compat wrapper — most call sites already use the outgoing form.
    function sanitizeOutgoingSdp(sdp) { return sanitizeSdp(sdp, 'out'); }
    function sanitizeIncomingSdp(sdp) { return sanitizeSdp(sdp, 'in'); }
    function escapeAttr(s) {
        return String(s).replace(/["<>&]/g, c => ({ '"': '&quot;', '<': '&lt;', '>': '&gt;', '&': '&amp;' }[c]));
    }

    // --- Polling for signals -----------------------------------------
    async function pollPendingSignals() {
        try {
            const res = await fetch('/call/pending', {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });
            if (!res.ok) return;
            const data = await res.json();
            for (const sig of (data.signals || [])) {
                const key = `${sig.callId}:${sig.type}:${sig.from}`;
                if (S.pollSeen.has(key)) continue;
                S.pollSeen.add(key);
                if (S.pollSeen.size > 500) {
                    S.pollSeen = new Set(Array.from(S.pollSeen).slice(-250));
                }
                if (typeof window.rtcHandleSignal === 'function') {
                    window.rtcHandleSignal(sig);
                }
            }
        } catch (_) {}
    }
    // Adaptive polling — idle at 500ms to keep incoming-ring latency low,
    // switch to 120ms during an active handshake so ICE trickle candidates
    // deliver near-instantly. Each candidate is a separate signal; at 500ms
    // the receive side of a 3-4 candidate exchange was waiting 1.5-2s just
    // for polling — ~half of the click-Accept → connected latency.
    const POLL_IDLE_MS   = 500;
    const POLL_ACTIVE_MS = 120;
    function pollIntervalMs() {
        // "Active" = any point from an incoming/outgoing call being
        // negotiated up to the moment it's fully connected. After
        // 'connected', slow back down — no more signaling traffic expected
        // until hangup / ICE restart.
        if (!S.pc) return POLL_IDLE_MS;
        const st = S.pc.connectionState;
        if (st === 'new' || st === 'connecting' || st === 'disconnected' || st === 'failed') return POLL_ACTIVE_MS;
        return POLL_IDLE_MS;
    }
    function startPolling() {
        if (S.pollTimer) return;
        const tick = () => {
            pollPendingSignals();
            S.pollTimer = setTimeout(tick, pollIntervalMs());
        };
        tick();
    }
    startPolling();

    // --- wire:navigate resilience ------------------------------------
    // When the user browses via `wire:navigate` mid-call, Livewire morphs
    // the layout blade — including the WebRTC UI container. That wipes the
    // `srcObject` bindings on the video/audio elements and any `.open`
    // class we added at runtime, even though the RTCPeerConnection and
    // MediaStream objects on window.__rtc keep flowing RTP throughout.
    //
    // On `livewire:navigated` (fires AFTER the new DOM is in place), we
    // re-open the UI and rebind the streams. To the user this is
    // seamless — audio may glitch for a frame but the call doesn't drop.
    // Doesn't help against full page reloads (regular <a href> links) —
    // those are unavoidable without a full-SPA rewrite.
    // Diagnostic — log every navigation event so we can see what fires
    // and in what order, and whether S state is intact by the time we
    // try to reattach the streams.
    document.addEventListener('livewire:navigating', () => {
        console.log('[rtc] livewire:navigating', {
            hasPc: !!S.pc,
            pcState: S.pc?.connectionState,
            callId: S.callId,
        });
    });
    document.addEventListener('livewire:navigate', () => {
        console.log('[rtc] livewire:navigate');
    });

    function restoreCallAfterNavigate(source) {
        const rtcCallEl = document.getElementById('rtcCall');
        console.log('[rtc] ' + source + ' — restore attempt', {
            hasPc: !!S.pc,
            pcState: S.pc?.connectionState,
            hasPeer: !!S.peer,
            hasRemoteAudio: !!S.remoteAudioStream,
            hasLocalStream: !!S.localStream,
            rtcCallExists: !!rtcCallEl,
            rtcCallHasOpen: rtcCallEl?.classList.contains('open'),
        });

        if (!S.pc && !S._pendingOffer) return;

        // Incoming banner state (ringing, not yet accepted) — re-render.
        if (S._pendingOffer && !S.pc && S.role === 'callee' && S.peer) {
            showIncoming({});
            return;
        }

        if (!S.pc || !S.peer) return;
        const st = S.pc.connectionState;
        if (st === 'closed' || st === 'failed') return;

        // Active call — re-render call card, then rebind media streams.
        const label = (st === 'connected') ? 'In call'
                    : (S.role === 'caller' ? 'Ringing…' : 'Connecting…');
        showCallUI(label);

        if (S.remoteAudioStream) {
            const ra = document.getElementById('rtcRemoteAudio');
            if (ra) { ra.srcObject = S.remoteAudioStream; ra.play().catch(() => {}); }
        }
        if (S.remoteVideoStream) {
            const rv = document.getElementById('rtcRemoteVideo');
            if (rv) { rv.srcObject = S.remoteVideoStream; rv.play().catch(() => {}); }
        }
        if (S.localStream && S.callType === 'video') {
            const lv = document.getElementById('rtcLocalVideo');
            if (lv) lv.srcObject = S.localStream;
        }
        if (st === 'connected' && S.connectedAt) {
            const timer = document.getElementById('rtcTimer');
            if (timer) timer.style.display = 'inline-block';
        }
        if (S.statsTicker) {
            const q = document.getElementById('rtcQuality');
            if (q) q.style.display = 'inline-flex';
        }
        console.log('[rtc] restore done');
    }

    document.addEventListener('livewire:navigated', () => restoreCallAfterNavigate('livewire:navigated'));

    // --- Bind UI buttons (delegated so they survive Livewire morphs) --
    document.addEventListener('click', (e) => {
        if (e.target.closest('#rtcAcceptBtn'))  { acceptIncoming(); return; }
        if (e.target.closest('#rtcDeclineBtn')) { declineIncoming(); return; }
        if (e.target.closest('#rtcHangupBtn'))  { hangup(); return; }

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

        if (e.target.closest('#rtcSpeakerBtn')) {
            const btn = e.target.closest('#rtcSpeakerBtn');
            toggleSpeaker(btn).catch(() => {});
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

        // Chat header call buttons
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
