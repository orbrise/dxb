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
            // Voice-first bitrate cap (24 kbps) — applied before the offer
            // is created so the initial SDP reflects our encoder limits.
            await capAudioBitrate(S.pc, 24000);

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
                // Renegotiation offer arriving on an active call (ICE-restart
                // OR mid-call switch). If a switch is in progress on this
                // side, attach our local track BEFORE answering so both the
                // sender's and receiver's new tracks land in one SDP round.
                if (S.pc && S.callId === e.callId) {
                    try {
                        console.log('[rtc-switch] renegotiation offer received, awaitingSwitch=' + S._awaitingSwitchOffer);
                        await S.pc.setRemoteDescription(new RTCSessionDescription({
                            type: e.payload.sdp.type || 'offer',
                            sdp: sanitizeIncomingSdp(normalizeSdp(e.payload.sdp.sdp)),
                        }));

                        // Mid-switch to VIDEO: make sure we have a camera
                        // track ready. acceptSwitch pre-acquires it, but
                        // as a safety net we also acquire here — otherwise
                        // if acceptSwitch's camera call failed silently
                        // (user dismissed permission late, device busy,
                        // etc.) the peer would never see our video.
                        if (S._awaitingSwitchOffer === 'video') {
                            if (!S.localStream || !S.localStream.getVideoTracks().length) {
                                try {
                                    if (!S.localStream) {
                                        S.localStream = await navigator.mediaDevices.getUserMedia({
                                            audio: true,
                                            video: { width: { ideal: 640 }, height: { ideal: 480 } },
                                        });
                                    } else {
                                        const cam = await navigator.mediaDevices.getUserMedia({
                                            video: { width: { ideal: 640 }, height: { ideal: 480 } },
                                        });
                                        S.localStream.addTrack(cam.getVideoTracks()[0]);
                                    }
                                } catch (camErr) {
                                    console.error('[rtc-switch] camera acquisition failed in offer handler', camErr);
                                    toast(mediaErrorMessage(camErr, 'camera'));
                                }
                            }
                            const videoTrack = S.localStream?.getVideoTracks?.()[0];
                            if (videoTrack) {
                                // Prefer attaching to the transceiver Chrome
                                // just created from the remote offer. Setting
                                // direction=sendrecv BEFORE replaceTrack is
                                // important: Chrome defaults new remote-driven
                                // transceivers to recvonly, which would silently
                                // keep the answer SDP recvonly and the peer
                                // would never receive our video.
                                const existingVideoTx = S.pc.getTransceivers().find(tx => tx.receiver?.track?.kind === 'video');
                                if (existingVideoTx && existingVideoTx.sender) {
                                    try { existingVideoTx.direction = 'sendrecv'; } catch (_) {}
                                    await existingVideoTx.sender.replaceTrack(videoTrack);
                                    console.log('[rtc-switch] attached camera via existing transceiver');
                                } else {
                                    S.pc.addTrack(videoTrack, S.localStream);
                                    console.log('[rtc-switch] attached camera via addTrack');
                                }
                            } else {
                                console.warn('[rtc-switch] no video track available to attach on receiver side');
                            }
                            S.callType = 'video';
                        } else if (S._awaitingSwitchOffer === 'audio') {
                            // Downgrade: stop our video senders.
                            S.pc.getSenders().forEach(sd => {
                                if (sd.track && sd.track.kind === 'video') {
                                    try { sd.replaceTrack(null); } catch (_) {}
                                }
                            });
                            if (S.localStream) {
                                S.localStream.getVideoTracks().forEach(t => {
                                    t.stop();
                                    try { S.localStream.removeTrack(t); } catch (_) {}
                                });
                            }
                            S.callType = 'audio';
                        }

                        const answer = await S.pc.createAnswer();
                        await S.pc.setLocalDescription(answer);
                        await postSignal('answer', { sdp: { type: answer.type, sdp: sanitizeOutgoingSdp(answer.sdp) } });
                        console.log('[rtc-switch] answer sent, callType=' + S.callType);

                        if (S._awaitingSwitchOffer) {
                            const to = S._awaitingSwitchOffer;
                            S._awaitingSwitchOffer = null;
                            S._suppressNegotiation = false;
                            showCallUI(document.getElementById('rtcCallState')?.textContent || 'In call');
                            attachLocalPreview();
                            updateSwitchBtn();
                            toast(to === 'video' ? 'Switched to video' : 'Switched to voice');
                        } else {
                            setState('Reconnecting…');
                        }
                    } catch (err) {
                        console.warn('[rtc] renegotiation answer failed', err);
                        S._awaitingSwitchOffer = null;
                        S._suppressNegotiation = false;
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

            // ------- Mid-call audio↔video switch (WhatsApp style) -------
            // Three-step handshake: initiator sends 'switch-request{to}',
            // responder shows prompt → sends 'switch-accept' or
            // 'switch-reject'. On accept, both sides perform the actual
            // media change + an SDP renegotiation driven by onnegotiationneeded.
            if (t === 'switch-request' || t === 'switch-accept' || t === 'switch-reject') {
                console.log('[rtc-switch] received ' + t, {
                    payload: e.payload, callId: e.callId, from: e.from,
                    myCallId: S.callId, myPending: S._pendingSwitch,
                });
            }
            if (t === 'switch-request') {
                if (!S.pc || S._pendingSwitch) {
                    // Already in a switch flow or no active call — reject.
                    await postSignal('switch-reject', { to: e.payload?.to || 'video' });
                    return;
                }
                const to = e.payload?.to === 'audio' ? 'audio' : 'video';
                if (to === S.callType) {
                    // Already in the requested mode — auto-accept to keep peers in sync.
                    await postSignal('switch-accept', { to });
                    return;
                }
                S._pendingSwitch = { to, role: 'receiver' };
                showSwitchPrompt(to);
                return;
            }

            if (t === 'switch-accept' && S._pendingSwitch?.role === 'sender') {
                const to = S._pendingSwitch.to;
                S._pendingSwitch = null;
                dismissToast();
                try {
                    await applyMediaSwitch(to);
                    toast(to === 'video' ? 'Switched to video' : 'Switched to voice');
                } catch (err) {
                    console.error('[rtc] applyMediaSwitch (sender) failed', err);
                    toast(mediaErrorMessage(err, to === 'video' ? 'camera' : 'microphone'));
                }
                updateSwitchBtn();
                return;
            }

            if (t === 'switch-reject' && S._pendingSwitch?.role === 'sender') {
                const target = S._pendingSwitch.to;
                S._pendingSwitch = null;
                dismissToast();
                toast(target === 'video' ? 'Video request declined.' : 'Voice request declined.');
                updateSwitchBtn();
                return;
            }

            // Caller tapped Cancel on their pending-switch toast — dismiss
            // our prompt (receiver side) so we don't ask about a request
            // that's no longer live.
            if (t === 'switch-cancel' && S._pendingSwitch?.role === 'receiver') {
                S._pendingSwitch = null;
                hideSwitchPrompt();
                updateSwitchBtn();
                return;
            }
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
            await capAudioBitrate(S.pc, 24000);

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

        // Fires when addTrack/removeTrack (or direction change) requires a
        // new offer/answer round. Used by the mid-call voice↔video switch.
        //
        // Only the SENDER (switch initiator) drives the renegotiation offer.
        // The receiver suppresses their own fire via S._suppressNegotiation
        // and instead attaches their track just-in-time inside the ICE-restart
        // offer branch of rtcHandleSignal, before creating the answer. This
        // avoids SDP glare where both sides race to send offers simultaneously.
        pc.onnegotiationneeded = async () => {
            if (!S.connectedAt) return;         // skip during initial handshake
            if (S._suppressNegotiation) return; // receiver's add-track fires it; sender drives
            if (S._makingOffer) return;         // already in a renegotiation round
            S._makingOffer = true;
            try {
                const offer = await S.pc.createOffer();
                await S.pc.setLocalDescription(offer);
                await postSignal('offer', { sdp: { type: offer.type, sdp: sanitizeOutgoingSdp(offer.sdp) } });
            } catch (err) {
                console.warn('[rtc] renegotiation failed', err);
            } finally {
                S._makingOffer = false;
            }
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
                // Switch button only makes sense once media is flowing.
                updateSwitchBtn();
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

    // --- Mid-call switch (voice ↔ video) ------------------------------
    // Called on BOTH sides after a successful handshake (sender on
    // switch-accept, receiver inside acceptSwitch()). Modifies the local
    // Convert a getUserMedia / getDisplayMedia error into a user-facing
    // message so the toast actually tells the user what went wrong
    // (permission vs. missing device vs. device-in-use) instead of a
    // generic "check camera permission" catch-all.
    function mediaErrorMessage(err, kind = 'camera') {
        const name = err && err.name ? err.name : '';
        switch (name) {
            case 'NotAllowedError':
            case 'SecurityError':
                return `${kind.charAt(0).toUpperCase() + kind.slice(1)} access was blocked. Allow it in the browser's address-bar permissions.`;
            case 'NotFoundError':
            case 'OverconstrainedError':
                return `No ${kind} found on this device.`;
            case 'NotReadableError':
            case 'TrackStartError':
                return `${kind.charAt(0).toUpperCase() + kind.slice(1)} is in use by another app or browser tab.`;
            case 'AbortError':
                return `${kind.charAt(0).toUpperCase() + kind.slice(1)} startup was interrupted. Try again.`;
            default:
                return err?.message ? `Could not open ${kind}: ${err.message}` : `Could not open ${kind}.`;
        }
    }

    // stream + peer connection in place; onnegotiationneeded fires an
    // offer/answer renegotiation automatically if needed.
    async function applyMediaSwitch(to) {
        if (!S.pc) return;

        // Suppress the automatic onnegotiationneeded path BEFORE any
        // addTrack / setDirection call. Chrome fires the event as a
        // microtask immediately after addTrack; if the flag isn't set
        // first, we end up racing our manual createOffer below against
        // the event handler's createOffer and one of them fails with
        // InvalidStateError, leaving the peer with no new SDP.
        S._suppressNegotiation = true;
        S._makingOffer = true;

        try {
            if (to === 'video') {
                // Add a camera track to the existing stream + peer connection.
                if (!S.localStream) {
                    S.localStream = await navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: { width: { ideal: 640 }, height: { ideal: 480 } },
                    });
                } else if (!S.localStream.getVideoTracks().length) {
                    const camStream = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 } },
                    });
                    const videoTrack = camStream.getVideoTracks()[0];
                    S.localStream.addTrack(videoTrack);
                    const existingVideoTx = S.pc.getTransceivers().find(tx => tx.receiver?.track?.kind === 'video');
                    if (existingVideoTx && existingVideoTx.sender) {
                        try { existingVideoTx.direction = 'sendrecv'; } catch (_) {}
                        await existingVideoTx.sender.replaceTrack(videoTrack);
                    } else {
                        S.pc.addTrack(videoTrack, S.localStream);
                    }
                }
                S.callType = 'video';
            } else {
                // Downgrade — stop + remove our video track(s). Peer's track
                // keeps flowing unless they also downgrade (which they do,
                // since both sides call applyMediaSwitch after an accept).
                if (S.localStream) {
                    S.localStream.getVideoTracks().forEach(t => {
                        t.stop();
                        try { S.localStream.removeTrack(t); } catch (_) {}
                    });
                }
                S.pc.getSenders().forEach(sender => {
                    if (sender.track && sender.track.kind === 'video') {
                        try { sender.replaceTrack(null); } catch (_) {}
                    }
                });
                S.callType = 'audio';
            }

            // Refresh the UI panel immediately so the caller sees the new
            // state (shows/hides video elements, swaps icons).
            showCallUI(document.getElementById('rtcCallState')?.textContent || 'In call');
            attachLocalPreview();
            updateSwitchBtn();

            // Drive the renegotiation ourselves rather than waiting for
            // onnegotiationneeded. Chrome occasionally skips the event
            // (prior pending offer, connectedAt check, etc.), and when
            // that happens the sender silently has a new track but never
            // tells the peer about it — classic "I see my own video but
            // they don't see me" bug.
            if (S.pc.signalingState !== 'stable') {
                console.warn('[rtc-switch] signalingState not stable, skipping createOffer:', S.pc.signalingState);
            } else {
                const offer = await S.pc.createOffer();
                await S.pc.setLocalDescription(offer);
                await postSignal('offer', { sdp: { type: offer.type, sdp: sanitizeOutgoingSdp(offer.sdp) } });
                console.log('[rtc-switch] renegotiation offer sent, callType=' + S.callType);
            }
        } catch (err) {
            console.error('[rtc-switch] applyMediaSwitch failed', err);
            throw err; // bubble up so the caller handler can toast an error
        } finally {
            S._makingOffer = false;
            S._suppressNegotiation = false;
        }
    }

    // --- Switch button + prompt UI helpers ----------------------------
    function updateSwitchBtn() {
        const btn = document.getElementById('rtcSwitchBtn');
        if (!btn) return;
        // Only visible during VOICE calls — it's the "upgrade to video"
        // affordance. During video calls the dedicated Video toggle
        // (rtcVideoBtn) occupies the same control-sheet slot, so we hide
        // this one to avoid two buttons stacking in the Video cell.
        if (!S.pc || S.callType === 'video') {
            btn.style.display = 'none';
            btn.classList.remove('is-pending');
            return;
        }
        btn.style.display = '';
        btn.title = 'Switch to video';
        btn.innerHTML = '<i class="fa fa-video"></i>';
        if (S._pendingSwitch) btn.classList.add('is-pending');
        else                  btn.classList.remove('is-pending');
    }
    function showSwitchPrompt(to) {
        const el = document.getElementById('rtcSwitchPrompt');
        if (!el) return;
        const title = document.getElementById('rtcSwitchPromptTitle');
        const sub   = document.getElementById('rtcSwitchPromptSub');
        const icon  = document.getElementById('rtcSwitchPromptIcon');
        const peerName = S.peer?.name || 'The other person';
        if (to === 'video') {
            if (title) title.textContent = 'Switch to video?';
            if (sub)   sub.textContent   = `${peerName} wants to turn on video.`;
            if (icon)  icon.className    = 'fa fa-video';
        } else {
            if (title) title.textContent = 'Switch to voice?';
            if (sub)   sub.textContent   = `${peerName} wants to turn off video.`;
            if (icon)  icon.className    = 'fa fa-phone';
        }
        el.classList.add('open');
    }
    function hideSwitchPrompt() {
        document.getElementById('rtcSwitchPrompt')?.classList.remove('open');
    }
    // Receiver side: Accept the pending switch.
    //
    // Flow: send switch-accept → mark that we're awaiting the sender's
    // renegotiation offer → add our local track (suppressed negotiation).
    // When the sender's offer arrives via the ICE-restart branch, we add
    // our track BEFORE creating the answer so both tracks land in one
    // negotiation round — no SDP glare.
    async function acceptSwitch() {
        if (!S._pendingSwitch || S._pendingSwitch.role !== 'receiver') return;
        const to = S._pendingSwitch.to;
        hideSwitchPrompt();
        S._pendingSwitch = null;

        // CRITICAL ordering: set the state flags + pre-acquire media
        // BEFORE sending the accept. Otherwise the sender posts a new
        // SDP offer the moment they get the accept and that offer can
        // race back to us before `_awaitingSwitchOffer` is set — in
        // which case the offer handler skips the "attach my camera
        // track" branch and the peer never sees our video.
        S._awaitingSwitchOffer = to;
        S._suppressNegotiation = true;

        try {
            if (to === 'video' && (!S.localStream || !S.localStream.getVideoTracks().length)) {
                if (!S.localStream) {
                    S.localStream = await navigator.mediaDevices.getUserMedia({
                        audio: true,
                        video: { width: { ideal: 640 }, height: { ideal: 480 } },
                    });
                } else {
                    const cam = await navigator.mediaDevices.getUserMedia({
                        video: { width: { ideal: 640 }, height: { ideal: 480 } },
                    });
                    S.localStream.addTrack(cam.getVideoTracks()[0]);
                }
            }
            // All local prep is done — now notify the sender. Any offer
            // they send back will find _awaitingSwitchOffer already set.
            await postSignal('switch-accept', { to });
        } catch (err) {
            console.error('[rtc] acceptSwitch failed', err);
            toast(mediaErrorMessage(err, to === 'video' ? 'camera' : 'microphone'));
            S._awaitingSwitchOffer = null;
            S._suppressNegotiation = false;
            try { await postSignal('switch-reject', { to }); } catch (_) {}
        }
        updateSwitchBtn();
    }
    // Receiver side: Decline the pending switch.
    async function rejectSwitch() {
        if (!S._pendingSwitch || S._pendingSwitch.role !== 'receiver') return;
        const to = S._pendingSwitch.to;
        hideSwitchPrompt();
        S._pendingSwitch = null;
        try { await postSignal('switch-reject', { to }); } catch (_) {}
    }
    // Sender side: Request the switch.
    async function requestSwitch() {
        console.log('[rtc-switch] requestSwitch called', {
            hasPc: !!S.pc, connectedAt: S.connectedAt,
            pending: S._pendingSwitch, callType: S.callType,
            callId: S.callId, peerId: S.peer?.id,
        });
        if (!S.pc || S._pendingSwitch) {
            console.warn('[rtc-switch] requestSwitch bailed — no pc or already pending');
            return;
        }
        // Pre-connect: the switch RPC rides the same signaling channel
        // as answer/ice; it only works once the peer is actually in the
        // call. Tell the caller to wait instead of silently no-op'ing.
        if (!S.connectedAt) {
            toast('Please wait until the call connects.');
            return;
        }
        const to = S.callType === 'video' ? 'audio' : 'video';
        S._pendingSwitch = { to, role: 'sender' };
        updateSwitchBtn();
        // WhatsApp-style persistent "asking Peter to turn on video…" banner.
        // Stays visible until the peer accepts/rejects or the caller taps
        // Cancel; the pulsing dot signals the wait state at a glance.
        const peerName = S.peer?.name || 'the other person';
        toast(
            to === 'video'
                ? `Asking ${peerName} to switch to a video call…`
                : `Asking ${peerName} to switch to a voice call…`,
            {
                persistent: true,
                cancelLabel: 'Cancel',
                onCancel: () => {
                    if (!S._pendingSwitch || S._pendingSwitch.role !== 'sender') return;
                    S._pendingSwitch = null;
                    updateSwitchBtn();
                    // Best-effort tell the peer to drop the prompt on their side.
                    postSignal('switch-cancel', {}).catch(() => {});
                },
            }
        );
        try {
            await postSignal('switch-request', { to });
            console.log('[rtc-switch] switch-request sent, to=' + to);
        } catch (err) {
            console.error('[rtc-switch] switch-request POST failed', err);
            S._pendingSwitch = null;
            updateSwitchBtn();
            dismissToast();
            toast('Could not send switch request: ' + err.message);
        }
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

    // --- Screen share -------------------------------------------------
    // Swaps the outgoing video sender's track between the camera and a
    // getDisplayMedia() screen capture. No signaling renegotiation needed —
    // replaceTrack() on the RTCRtpSender keeps the SDP intact. Only enabled
    // during video calls (voice calls have no video sender to swap).
    //
    // iOS Safari doesn't expose getDisplayMedia — we detect that upfront
    // and show a friendly toast rather than throwing a cryptic error.
    async function toggleScreenShare() {
        if (S._screenShare) {
            stopScreenShare();
            return;
        }
        await startScreenShare();
    }

    async function startScreenShare() {
        if (S._screenShare) return;
        if (S.callType !== 'video' || !S.pc) {
            toast('Screen sharing is only available during video calls.');
            return;
        }
        if (!navigator.mediaDevices?.getDisplayMedia) {
            toast('Screen sharing isn\'t supported on this browser.');
            return;
        }

        const videoSender = S.pc.getSenders().find(s => s.track && s.track.kind === 'video');
        if (!videoSender) {
            toast('Video track not ready. Try again in a moment.');
            return;
        }

        let screenStream;
        try {
            screenStream = await navigator.mediaDevices.getDisplayMedia({
                video: { frameRate: { ideal: 15, max: 30 } },
                audio: false,
            });
        } catch (err) {
            // User cancelled the picker, or permission denied — both are
            // treated as a soft decline, no toast needed. Real errors
            // get logged.
            if (err && err.name !== 'NotAllowedError' && err.name !== 'AbortError') {
                console.warn('[rtc-share] getDisplayMedia failed', err);
                toast('Could not start screen share.');
            }
            return;
        }

        const screenTrack = screenStream.getVideoTracks()[0];
        if (!screenTrack) {
            screenStream.getTracks().forEach(t => t.stop());
            toast('Could not start screen share.');
            return;
        }

        // Remember the original camera track so we can restore it later.
        const cameraTrack = videoSender.track;
        S._screenShare = { stream: screenStream, cameraTrack, videoSender };

        try {
            await videoSender.replaceTrack(screenTrack);
        } catch (err) {
            console.error('[rtc-share] replaceTrack failed', err);
            screenStream.getTracks().forEach(t => t.stop());
            S._screenShare = null;
            toast('Could not start screen share.');
            return;
        }

        // Mirror the shared screen in the local preview so the user can
        // see what the peer sees. Camera preview resumes on stop.
        const lv = document.getElementById('rtcLocalVideo');
        if (lv) {
            lv.srcObject = screenStream;
            lv.style.transform = 'none'; // disable mirror flip for screen content
        }

        // Browser-native "Stop sharing" bar triggers onended on the track;
        // route that back through our stop path so UI stays in sync.
        screenTrack.addEventListener('ended', () => stopScreenShare(), { once: true });

        const btn = document.getElementById('rtcShareBtn');
        if (btn) {
            btn.classList.add('sharing');
            btn.title = 'Stop sharing';
            btn.setAttribute('aria-label', 'Stop sharing');
        }
        const lbl = document.getElementById('rtcShareLabel');
        if (lbl) lbl.textContent = 'Stop';
        toast('You\'re sharing your screen.');
    }

    function stopScreenShare() {
        const sh = S._screenShare;
        if (!sh) return;
        S._screenShare = null;

        // Stop the capture so the browser's indicator / tab-sharing bar clears.
        try { sh.stream.getTracks().forEach(t => t.stop()); } catch (_) {}

        // Restore the camera track on the same sender — no renegotiation.
        if (sh.videoSender && sh.cameraTrack) {
            sh.videoSender.replaceTrack(sh.cameraTrack).catch(err => {
                console.warn('[rtc-share] restoring camera track failed', err);
            });
        }

        // Restore local preview to the camera stream (mirrored).
        const lv = document.getElementById('rtcLocalVideo');
        if (lv) {
            lv.srcObject = S.localStream;
            lv.style.transform = ''; // re-enable the CSS mirror flip
        }

        const btn = document.getElementById('rtcShareBtn');
        if (btn) {
            btn.classList.remove('sharing');
            btn.title = 'Share screen';
            btn.setAttribute('aria-label', 'Share screen');
        }
        const lbl = document.getElementById('rtcShareLabel');
        if (lbl) lbl.textContent = 'Share';
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

            // WhatsApp-style weak-connection alert. We only pop the toast
            // on the first transition into "poor" so a sustained bad
            // network doesn't spam a toast every 2 seconds. The toast
            // self-dismisses after ~4s; when the connection recovers to
            // "good"/"fair" we reset the latch so a future dip alerts again.
            if (tier === 'poor') {
                if (!S._weakAlertShown) {
                    S._weakAlertShown = true;
                    toast('Weak connection — audio or video may stutter.');
                }
            } else if (tier === 'good') {
                S._weakAlertShown = false;
            }
        }, 2000);
    }

    // --- Timer --------------------------------------------------------
    // Writes the elapsed time into BOTH #rtcTimer (legacy top-right slot,
    // hidden by CSS in the new WhatsApp layout) and #rtcCallState (the
    // line directly under the peer's name). The state line held "Ringing…"
    // / "Connecting…" during setup; once we're connected the ticker takes
    // over so the under-name slot flips to e.g. "02:55" like WhatsApp.
    function startTimer() {
        const timerEl = document.getElementById('rtcTimer');
        const stateEl = document.getElementById('rtcCallState');
        if (timerEl) timerEl.style.display = 'inline-block';
        S.stateTicker = setInterval(() => {
            const s = Math.floor((Date.now() - S.connectedAt) / 1000);
            const txt = `${String(Math.floor(s / 60)).padStart(2, '0')}:${String(s % 60).padStart(2, '0')}`;
            if (timerEl) timerEl.textContent = txt;
            if (stateEl) stateEl.textContent = txt;
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
        // Share-screen button stays visible on both call types. If the
        // user taps it during a voice call, startScreenShare() shows a
        // friendly "video calls only" toast — same as WhatsApp.
        const shareBtn = document.getElementById('rtcShareBtn');
        if (shareBtn) shareBtn.style.display = '';
        if (S.callType === 'audio') {
            if (ao) ao.style.display = 'block';
            if (rv) rv.style.display = 'none';
            if (videoBtn)  videoBtn.style.display  = 'none';
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
            if (videoBtn)  videoBtn.style.display  = '';
        }
        // Recompute switch button visibility whenever the call panel renders —
        // handles Livewire morph / navigation restore where this fn runs again
        // after the call has already connected.
        updateSwitchBtn();
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
    /**
     * Show a status toast at the top of the call UI.
     *
     * Default: transient — fades in, holds ~3.5s, fades out.
     * With `{ persistent: true }`: stays open until `dismissToast()` is
     *     called (used for "waiting for peer…" style messages).
     * With `{ cancelLabel, onCancel }`: adds an inline cancel link
     *     (used so the caller can back out of a pending switch request).
     */
    function toast(msg, opts = {}) {
        const el = document.getElementById('rtcToast');
        if (!el) return;
        el.classList.remove('open', 'persistent');
        void el.offsetWidth;

        const persistent = !!opts.persistent;
        const parts = [];
        if (persistent) parts.push('<span class="rtc-toast-dot"></span>');
        parts.push('<span class="rtc-toast-msg"></span>');
        if (opts.cancelLabel) parts.push('<button type="button" class="rtc-toast-cancel"></button>');
        el.innerHTML = parts.join('');
        el.querySelector('.rtc-toast-msg').textContent = msg;
        if (opts.cancelLabel) {
            const btn = el.querySelector('.rtc-toast-cancel');
            btn.textContent = opts.cancelLabel;
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                dismissToast();
                if (typeof opts.onCancel === 'function') {
                    try { opts.onCancel(); } catch (err) { console.warn('[toast] onCancel failed', err); }
                }
            });
        }
        el.classList.add('open');
        if (persistent) el.classList.add('persistent');
    }
    function dismissToast() {
        const el = document.getElementById('rtcToast');
        if (!el) return;
        el.classList.remove('open', 'persistent');
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
        // Make sure any live screen capture is stopped BEFORE the pc + stream
        // teardown — otherwise the browser's "sharing your screen" bar
        // outlives the call. stopScreenShare() is a no-op if we weren't sharing.
        try { stopScreenShare(); } catch (_) {}
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
        S._pendingSwitch = null;
        S._awaitingSwitchOffer = null;
        S._suppressNegotiation = false;
        S._makingOffer = false;
        hideSwitchPrompt();
        dismissToast();
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

    // Tune the audio m= section for reliability on slow / lossy links.
    // Applied to BOTH outgoing offer and answer — both peers need matching
    // Opus params for FEC/DTX to actually engage, and both need the
    // bandwidth cap in their SDP so the far side doesn't blast us.
    //
    // Changes made per Opus fmtp line:
    //   useinbandfec=1  → Forward Error Correction, recovers lost packets
    //                     by re-transmitting a copy inside the next packet
    //   usedtx=1        → Discontinuous transmission (silence suppression)
    //                     so no audio bytes flow while nobody's talking
    //   stereo=0        → Mono, half the bandwidth of stereo
    //   maxaveragebitrate=24000 → cap Opus at 24 kbps (near-toll quality)
    //   maxplaybackrate=16000   → wideband (16 kHz) instead of full 48 kHz
    //   cbr=0           → let Opus adapt bitrate within the cap
    //
    // Also inserts a `b=AS:32` (32 kbps ceiling for the audio session).
    // Combined, this gives us reliable voice down to ~50 kbps links with
    // 10-20% packet loss — well beyond what stock WebRTC survives.
    function optimizeAudioSdp(sdp) {
        if (!sdp) return sdp;
        const lines = sdp.split(/\r?\n/);

        // Find the Opus payload type.
        let inAudio = false;
        let opusPT = null;
        for (const line of lines) {
            if (line.startsWith('m=audio ')) inAudio = true;
            else if (line.startsWith('m=')) inAudio = false;
            if (inAudio && line.startsWith('a=rtpmap:')) {
                const m = line.match(/^a=rtpmap:(\d+)\s+opus/i);
                if (m) { opusPT = m[1]; break; }
            }
        }
        if (!opusPT) return sdp; // No Opus, nothing to tune

        const desired = {
            useinbandfec:       '1',
            usedtx:             '1',
            stereo:             '0',
            maxaveragebitrate:  '24000',
            maxplaybackrate:    '16000',
            cbr:                '0',
        };

        const out = [];
        inAudio = false;
        let insertedBandwidth = false;
        let touchedFmtp = false;

        for (const line of lines) {
            if (line.startsWith('m=audio ')) {
                inAudio = true;
                out.push(line);
                if (!insertedBandwidth) {
                    // 32 kbps ceiling — a bit above our maxaveragebitrate
                    // to leave room for FEC + RTCP overhead.
                    out.push('b=AS:32');
                    insertedBandwidth = true;
                }
                continue;
            }
            if (line.startsWith('m=')) inAudio = false;

            if (inAudio && line.startsWith('a=fmtp:' + opusPT)) {
                // Merge our params into any that already exist.
                const parts = line.split(' ');
                const prefix = parts[0];
                const params = parts.slice(1).join(' ').split(';').map(s => s.trim()).filter(Boolean);
                for (const [k, v] of Object.entries(desired)) {
                    const i = params.findIndex(p => p.split('=')[0] === k);
                    if (i >= 0) params[i] = k + '=' + v;
                    else params.push(k + '=' + v);
                }
                out.push(prefix + ' ' + params.join(';'));
                touchedFmtp = true;
                continue;
            }
            out.push(line);
        }

        // If WebRTC didn't emit an fmtp:<opus> line at all, add one right
        // after its rtpmap so our params still land. Rare but happens on
        // some Safari builds.
        if (!touchedFmtp) {
            const paramStr = Object.entries(desired).map(([k, v]) => k + '=' + v).join(';');
            const insertAt = out.findIndex(l => l.startsWith('a=rtpmap:' + opusPT));
            if (insertAt >= 0) out.splice(insertAt + 1, 0, 'a=fmtp:' + opusPT + ' ' + paramStr);
        }

        return out.join('\r\n');
    }

    // Backwards-compat wrapper — most call sites already use the outgoing form.
    // Composed with optimizeAudioSdp so BOTH sanitize (private-IP fixes) AND
    // audio tuning apply to every outgoing SDP.
    function sanitizeOutgoingSdp(sdp) { return optimizeAudioSdp(sanitizeSdp(sdp, 'out')); }
    function sanitizeIncomingSdp(sdp) { return sanitizeSdp(sdp, 'in'); }

    // Hard-cap the audio sender bitrate via the encoder parameters API.
    // Complements the SDP maxaveragebitrate — modern WebRTC honours this
    // setting even if the SDP is ignored, and works both for the initial
    // encoding and any subsequent codec renegotiations. Called right
    // after addTrack() so the first packet already respects the cap.
    async function capAudioBitrate(pc, maxBps) {
        if (!pc || !pc.getSenders) return;
        for (const sender of pc.getSenders()) {
            if (!sender.track || sender.track.kind !== 'audio') continue;
            try {
                const params = sender.getParameters();
                if (!params.encodings || !params.encodings.length) params.encodings = [{}];
                params.encodings[0].maxBitrate = maxBps;
                params.encodings[0].priority = 'high';   // give voice bandwidth priority
                params.encodings[0].networkPriority = 'high';
                await sender.setParameters(params);
            } catch (err) {
                console.warn('[rtc] capAudioBitrate failed', err);
            }
        }
    }
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
    // Adaptive polling. Idle poll is a *safety net* for incoming calls when
    // the Reverb WebSocket drops a frame; real-time delivery is Reverb's job.
    // 500ms idle was murdering an 8-worker LiteSpeed pool — 2 requests per
    // second per authenticated tab, forever, on EVERY page (not just chat).
    // With 5 logged-in users browsing the site that's 10 req/s of pure
    // polling, which was starving the pool and making guest page loads
    // queue 3+ seconds behind the pollers. 15s idle gives a worst-case
    // 15s "missed call" delay on WebSocket-dropped environments, which is
    // acceptable; the active-handshake rate stays tight so in-progress
    // ICE exchanges still feel instant.
    const POLL_IDLE_MS   = 15000;
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
    // PROD PERF: await the fetch BEFORE scheduling the next tick. The old
    // version called pollPendingSignals() without awaiting then immediately
    // scheduled setTimeout 500ms later — so if the server was slow (even
    // 1s), requests piled up unbounded, hammered PHP-FPM workers, and the
    // whole chat page ground to a halt in a feedback loop. Serialized
    // polling self-throttles: slow server = slower polling, instead of
    // concurrent polls DDoSing the server. If the previous poll is still
    // in flight, we skip rather than queue.
    function startPolling() {
        if (S.pollTimer) return;
        const tick = async () => {
            try { await pollPendingSignals(); } catch (_) {}
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

        if (e.target.closest('#rtcSwitchBtn'))        { requestSwitch(); return; }
        if (e.target.closest('#rtcSwitchAcceptBtn'))  { acceptSwitch(); return; }
        if (e.target.closest('#rtcSwitchRejectBtn'))  { rejectSwitch(); return; }

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

        if (e.target.closest('#rtcShareBtn')) {
            toggleScreenShare();
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
