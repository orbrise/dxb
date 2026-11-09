{{-- Trim + thumbnail modal — opens for every video picked in #mphoto, on
     both new-profile and user-profile (edit). Lets the user choose a
     continuous sub-clip (duration bounded so the estimated output ≤ 100 MB,
     the Cloudflare upload limit, based on the source bitrate) and capture
     a thumbnail frame. On confirm we re-encode the chosen range via
     MediaRecorder (video.captureStream) and feed the resulting Blob back
     into #mphoto so the normal Livewire upload pipeline picks it up; the
     captured frame reaches the server through _profile-video-thumbnailer.

     The entire feature is client-side: no new backend endpoints, no FFmpeg
     install on the server. Include it inside the page's Livewire component,
     after #mphoto. wire:ignore keeps a re-render from hiding the open modal
     or swapping the elements the script holds. --}}
<div id="trimModalOverlay" wire:ignore style="display:none;position:fixed;inset:0;background:rgba(10,10,10,0.72);z-index:100060;align-items:flex-start;justify-content:center;padding:24px 16px;overflow-y:auto;">
    <div id="trimModal" style="background:#fff;color:#0f172a;width:100%;max-width:720px;border-radius:14px;box-shadow:0 20px 60px rgba(0,0,0,0.5);overflow:hidden;">
        <div style="padding:18px 22px;border-bottom:1px solid #e2e8f0;display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
            <div>
                <h3 style="margin:0 0 4px;font-size:18px;font-weight:700;">Edit ad video</h3>
                <p style="margin:0;font-size:13px;color:#64748b;">Trim the clip and pick a thumbnail</p>
            </div>
            <button type="button" id="trimCloseBtn" aria-label="Close" style="background:transparent;border:none;font-size:22px;line-height:1;color:#64748b;cursor:pointer;padding:4px 8px;">&times;</button>
        </div>

        <div style="margin:16px 22px 0;padding:12px 14px;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;border-radius:8px;font-size:13px;display:flex;align-items:flex-start;gap:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;margin-top:1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <div>
                <strong style="font-weight:700;">Maximum video size: 100 MB.</strong>
                Larger files must be trimmed below to fit the limit before upload.
            </div>
        </div>

        <div style="padding:16px 22px 0;">
            <video id="trimVideo" controls playsinline style="width:100%;background:#000;border-radius:6px;max-height:300px;"></video>
        </div>

        <div style="margin:14px 22px 0;background:#f1f5f9;border-radius:8px;padding:14px 18px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;text-align:center;">
            <div>
                <div style="font-size:11px;letter-spacing:0.08em;color:#64748b;font-weight:600;">SOURCE</div>
                <div id="trimSrcDur" style="font-size:16px;font-weight:700;color:#0f172a;">—</div>
            </div>
            <div>
                <div style="font-size:11px;letter-spacing:0.08em;color:#64748b;font-weight:600;">CLIP</div>
                <div id="trimClipDur" style="font-size:16px;font-weight:700;color:#2563eb;">—</div>
            </div>
            <div>
                <div style="font-size:11px;letter-spacing:0.08em;color:#64748b;font-weight:600;">MAX</div>
                <div id="trimMaxDur" style="font-size:16px;font-weight:700;color:#0f172a;">—</div>
            </div>
        </div>

        <div style="margin:14px 22px 0;border:1px solid #e2e8f0;border-radius:10px;padding:16px 18px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <strong style="font-size:14px;">Trim</strong>
                <div style="font-size:13px;color:#64748b;">
                    Start <span id="trimStartLbl" style="color:#0f172a;font-weight:600;">00:00.0</span>
                    &nbsp;→&nbsp;
                    End <span id="trimEndLbl" style="color:#0f172a;font-weight:600;">00:00.0</span>
                </div>
            </div>
            {{-- Dual-range slider. Two <input type="range"> on top of each other
                 with a highlighted track rendered via a background gradient on the
                 wrapper. --}}
            <div id="trimSliderWrap" style="position:relative;height:28px;">
                <div id="trimSliderTrack" style="position:absolute;left:0;right:0;top:50%;height:4px;transform:translateY(-50%);background:#e2e8f0;border-radius:999px;"></div>
                <div id="trimSliderFill" style="position:absolute;top:50%;height:4px;transform:translateY(-50%);background:#60a5fa;border-radius:999px;left:0;right:0;"></div>
                <input id="trimStart" type="range" min="0" max="1000" value="0" step="1" style="position:absolute;inset:0;width:100%;pointer-events:auto;background:transparent;-webkit-appearance:none;appearance:none;">
                <input id="trimEnd"   type="range" min="0" max="1000" value="1000" step="1" style="position:absolute;inset:0;width:100%;pointer-events:auto;background:transparent;-webkit-appearance:none;appearance:none;">
            </div>
            <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:12px;color:#64748b;">
                <span>0:00</span>
                <span id="trimDurEndLbl">—</span>
            </div>
            <div id="trimHint" style="margin-top:12px;padding:10px 12px;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;font-size:13px;border-radius:8px;display:none;"></div>
        </div>

        <div style="margin:14px 22px 0;border:1px solid #e2e8f0;border-radius:10px;padding:14px 18px;display:flex;align-items:center;gap:16px;">
            <div style="flex:1;min-width:0;">
                <strong style="font-size:14px;display:block;">Thumbnail</strong>
                <div style="font-size:12px;color:#64748b;">Capture the current video frame</div>
            </div>
            <button type="button" id="trimCaptureBtn" style="background:#fff;color:#0f172a;border:1px solid #cbd5e1;border-radius:8px;padding:8px 14px;font-size:13px;cursor:pointer;">Capture frame</button>
        </div>
        <div style="margin:10px 22px 0;">
            <canvas id="trimThumbCanvas" style="display:none;"></canvas>
            <img id="trimThumbPreview" alt="" style="display:none;max-width:160px;border:1px solid #e2e8f0;border-radius:8px;">
        </div>

        <div style="padding:18px 22px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:12px;margin-top:16px;">
            <button type="button" id="trimCancelBtn" style="background:transparent;color:#0f172a;border:1px solid #cbd5e1;border-radius:8px;padding:9px 18px;font-size:14px;font-weight:600;cursor:pointer;">Cancel</button>
            <button type="button" id="trimUseBtn" style="background:#2563eb;color:#fff;border:none;border-radius:8px;padding:9px 20px;font-size:14px;font-weight:600;cursor:pointer;">Use video</button>
        </div>
    </div>
</div>

{{-- Trim modal controller. Separate IIFE so a failure here can't
     break the normal upload path. Document-level capture-phase
     listener on #mphoto change — fires BEFORE Livewire's own
     wire:model handler + the fallback uploader above, so when
     the picked video is > 100 MB we can stopImmediatePropagation,
     open the trim UI, and only re-dispatch change AFTER the
     trimmed Blob is back in #mphoto (then size is < 100 MB and
     this handler no-ops, letting the event flow normally). --}}
<script>
(function () {
    var MAX_BYTES    = 100 * 1024 * 1024; // Hard server cap.
    var TARGET_BYTES =  95 * 1024 * 1024; // Safety margin for re-encode.

    function $(id) { return document.getElementById(id); }

    function init() {
        var fileInput  = $('mphoto');
        var overlay    = $('trimModalOverlay');
        if (!fileInput || !overlay) return;
        // Guard per page, not per window: after a wire:navigate between the
        // new and edit pages a window flag would leave the new page unwired.
        if (overlay.dataset.trimWired === '1') return;
        overlay.dataset.trimWired = '1';

        var closeBtn   = $('trimCloseBtn');
        var cancelBtn  = $('trimCancelBtn');
        var useBtn     = $('trimUseBtn');
        var captureBtn = $('trimCaptureBtn');
        var video      = $('trimVideo');
        var startInput = $('trimStart');
        var endInput   = $('trimEnd');
        var fill       = $('trimSliderFill');
        var startLbl   = $('trimStartLbl');
        var endLbl     = $('trimEndLbl');
        var srcDurEl   = $('trimSrcDur');
        var clipDurEl  = $('trimClipDur');
        var maxDurEl   = $('trimMaxDur');
        var durEndLbl  = $('trimDurEndLbl');
        var hint       = $('trimHint');
        var thumbCanvas  = $('trimThumbCanvas');
        var thumbPreview = $('trimThumbPreview');

        var currentFile = null;
        var currentSrcUrl = null;
        var otherFiles = [];
        var maxDurationSec = 0;
        var sourceDurationSec = 0;
        var thumbBlob = null;

        function fmtTime(sec) {
            if (!isFinite(sec) || sec < 0) sec = 0;
            var m = Math.floor(sec / 60);
            var s = Math.floor(sec % 60);
            var t = Math.floor((sec * 10) % 10);
            return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s + '.' + t;
        }

        function openModal(file, others) {
            currentFile = file;
            otherFiles = others || [];
            thumbBlob = null;
            thumbPreview.style.display = 'none';
            if (currentSrcUrl) { URL.revokeObjectURL(currentSrcUrl); }
            currentSrcUrl = URL.createObjectURL(file);
            video.src = currentSrcUrl;
            overlay.style.display = 'flex';
            hint.style.display = 'none';
        }

        function closeModal() {
            overlay.style.display = 'none';
            try { video.pause(); } catch (e) {}
            video.removeAttribute('src');
            video.load();
            if (currentSrcUrl) { URL.revokeObjectURL(currentSrcUrl); currentSrcUrl = null; }
            currentFile = null;
            otherFiles = [];
        }

        video.addEventListener('loadedmetadata', function () {
            sourceDurationSec = video.duration || 0;
            if (!sourceDurationSec) return;
            var sourceBytes = currentFile ? currentFile.size : 0;
            var bitrate = sourceBytes / sourceDurationSec; // bytes/sec
            maxDurationSec = Math.max(1, Math.floor(TARGET_BYTES / bitrate));
            if (maxDurationSec > sourceDurationSec) maxDurationSec = sourceDurationSec;
            srcDurEl.textContent  = fmtTime(sourceDurationSec);
            maxDurEl.textContent  = fmtTime(maxDurationSec);
            durEndLbl.textContent = fmtTime(sourceDurationSec);
            startInput.value = 0;
            endInput.value   = 1000;
            updateFromSlider('end');
        });

        function sliderToTime(val) { return (val / 1000) * sourceDurationSec; }
        function timeToSlider(t)   { return Math.round((t / sourceDurationSec) * 1000); }

        function updateFromSlider(dragged) {
            var s = parseInt(startInput.value, 10);
            var e = parseInt(endInput.value, 10);
            if (s >= e) {
                if (dragged === 'start') s = Math.max(0, e - 1);
                else                     e = Math.min(1000, s + 1);
                startInput.value = s;
                endInput.value   = e;
            }
            var startTime = sliderToTime(s);
            var endTime   = sliderToTime(e);
            var clip      = endTime - startTime;

            if (clip > maxDurationSec + 0.05) {
                if (dragged === 'start') {
                    startTime = endTime - maxDurationSec;
                    startInput.value = timeToSlider(startTime);
                } else {
                    endTime = startTime + maxDurationSec;
                    endInput.value = timeToSlider(endTime);
                }
                clip = maxDurationSec;
                hint.style.display = 'block';
                hint.textContent = 'Video is longer than ' + Math.floor(maxDurationSec) + 's. Drag the blue handles to pick a continuous clip of up to ' + Math.floor(maxDurationSec) + ' seconds. The end handle cannot move before the start.';
            } else {
                hint.style.display = 'none';
            }

            var leftPct  = (parseInt(startInput.value, 10) / 1000) * 100;
            var rightPct = (parseInt(endInput.value, 10)   / 1000) * 100;
            fill.style.left  = leftPct + '%';
            fill.style.right = (100 - rightPct) + '%';

            startLbl.textContent  = fmtTime(startTime);
            endLbl.textContent    = fmtTime(endTime);
            clipDurEl.textContent = fmtTime(clip);

            // Seek preview to the start when the start handle moves, end
            // handle when the end moves, so the user can scrub by eye.
            try { video.currentTime = dragged === 'end' ? endTime : startTime; } catch (err) {}
        }

        startInput.addEventListener('input', function () { updateFromSlider('start'); });
        endInput  .addEventListener('input', function () { updateFromSlider('end'); });

        captureBtn.addEventListener('click', function () {
            if (!video.videoWidth) return;
            thumbCanvas.width  = video.videoWidth;
            thumbCanvas.height = video.videoHeight;
            thumbCanvas.getContext('2d').drawImage(video, 0, 0, thumbCanvas.width, thumbCanvas.height);
            thumbCanvas.toBlob(function (blob) {
                if (!blob) return;
                thumbBlob = blob;
                thumbPreview.src = URL.createObjectURL(blob);
                thumbPreview.style.display = 'block';
            }, 'image/jpeg', 0.85);
        });

        closeBtn.addEventListener('click', function () {
            closeModal();
            fileInput.value = '';
        });
        cancelBtn.addEventListener('click', function () {
            closeModal();
            fileInput.value = '';
        });

        async function trimViaMediaRecorder(startTime, endTime, origFile, onProgress) {
            // Clone to a hidden <video> so recording doesn't fight the
            // preview player's user interactions.
            var hidden = document.createElement('video');
            hidden.src = currentSrcUrl;
            hidden.muted = false;
            hidden.playsInline = true;
            hidden.preload = 'auto';
            hidden.style.position = 'fixed';
            hidden.style.top = '-9999px';
            hidden.style.left = '-9999px';
            document.body.appendChild(hidden);

            await new Promise(function (resolve, reject) {
                hidden.addEventListener('loadedmetadata', resolve, { once: true });
                hidden.addEventListener('error', function () { reject(new Error('could not read source video')); }, { once: true });
            });

            hidden.currentTime = startTime;
            await new Promise(function (resolve) {
                hidden.addEventListener('seeked', resolve, { once: true });
            });

            var stream;
            if (typeof hidden.captureStream === 'function')       stream = hidden.captureStream();
            else if (typeof hidden.mozCaptureStream === 'function') stream = hidden.mozCaptureStream();
            else { document.body.removeChild(hidden); throw new Error('In-browser trimming not supported here. Please use a video under 100 MB.'); }

            var candidates = [
                'video/mp4;codecs=h264,aac',
                'video/webm;codecs=vp9,opus',
                'video/webm;codecs=vp8,opus',
                'video/webm',
            ];
            var mime = '';
            for (var i = 0; i < candidates.length; i++) {
                if (window.MediaRecorder && MediaRecorder.isTypeSupported(candidates[i])) { mime = candidates[i]; break; }
            }
            if (!mime) { document.body.removeChild(hidden); throw new Error('MediaRecorder not supported in this browser.'); }

            var rec = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: 2500000 });
            var chunks = [];
            rec.ondataavailable = function (ev) { if (ev.data && ev.data.size) chunks.push(ev.data); };
            var stopped = new Promise(function (resolve) { rec.onstop = resolve; });

            rec.start(250);
            try { await hidden.play(); } catch (e) {}

            var totalDur = Math.max(0.0001, endTime - startTime);
            await new Promise(function (resolve) {
                function tick() {
                    if (typeof onProgress === 'function') {
                        var pct = Math.min(100, Math.max(0, Math.round(((hidden.currentTime - startTime) / totalDur) * 100)));
                        onProgress(pct);
                    }
                    if (hidden.currentTime >= endTime - 0.02 || hidden.ended) {
                        try { hidden.pause(); } catch (e) {}
                        resolve();
                    } else {
                        requestAnimationFrame(tick);
                    }
                }
                tick();
            });

            if (typeof onProgress === 'function') onProgress(100);
            try { rec.stop(); } catch (e) {}
            await stopped;
            document.body.removeChild(hidden);

            var outExt  = mime.indexOf('mp4') !== -1 ? 'mp4' : 'webm';
            var outType = mime.split(';')[0];
            var base    = (origFile.name || 'video').replace(/\.[^.]+$/, '') || 'video';
            return new File(chunks, base + '-trimmed.' + outExt, { type: outType });
        }

        useBtn.addEventListener('click', async function () {
            if (!currentFile) return;
            var startTime = sliderToTime(parseInt(startInput.value, 10));
            var endTime   = sliderToTime(parseInt(endInput.value, 10));
            if (endTime - startTime <= 0) return;

            useBtn.disabled = true;
            var origLabel = useBtn.textContent;
            useBtn.textContent = 'Trimming… 0%';

            try {
                var trimmed = await trimViaMediaRecorder(startTime, endTime, currentFile, function (pct) {
                    useBtn.textContent = 'Trimming… ' + pct + '%';
                });
                var dt = new DataTransfer();
                dt.items.add(trimmed);
                // Captured thumbnail is NOT added as a separate file
                // (that produced a duplicate tile). Instead we stash
                // its data URL in a FIFO queue; the MutationObserver
                // further down this script watches #image-container
                // for new .profile-video-preview nodes added by the
                // Livewire morph and applies the queued thumb as the
                // video's `poster` attribute. Without this the tile
                // shows black because the trimmed webm's first few
                // frames are literally black (MediaRecorder warm-up).
                if (thumbBlob) {
                    try {
                        var durl = await new Promise(function (resolve, reject) {
                            var r = new FileReader();
                            r.onload = function () { resolve(r.result); };
                            r.onerror = reject;
                            r.readAsDataURL(thumbBlob);
                        });
                        window.__pendingVideoThumbs = window.__pendingVideoThumbs || [];
                        window.__pendingVideoThumbs.push(durl);
                    } catch (e) { /* non-fatal */ }
                }
                for (var i = 0; i < otherFiles.length; i++) dt.items.add(otherFiles[i]);
                fileInput.files = dt.files;
                closeModal();
                // Mark the next change as our own re-dispatch so the
                // interceptor skips it (otherwise it would catch the
                // trimmed video and re-open the modal in a loop).
                fileInput.dataset.trimReentry = '1';
                fileInput.dispatchEvent(new Event('change', { bubbles: true }));
            } catch (err) {
                console.error('[trim] failed', err);
                alert('Could not trim the video: ' + (err && err.message ? err.message : err));
            } finally {
                useBtn.disabled = false;
                useBtn.textContent = origLabel;
            }
        });

        // Interceptor — fires at document level in capture phase, so
        // it runs BEFORE any listener attached directly to #mphoto
        // (the fallback uploader above + Livewire's wire:model).
        // Opens the trim/thumbnail modal for EVERY selected video,
        // regardless of size — gives the user a chance to shorten
        // the clip and pick a cover frame on every upload. For
        // videos already under the 100 MB cap the max-duration
        // calc in loadedmetadata leaves the full length selectable
        // (sourceDurationSec), so the modal doesn't impose a cut
        // unless the user drags the handles. The hint banner only
        // appears when the handles are pulled past the byte budget.
        //
        // Trimmed output is a File written back into #mphoto; the
        // fresh `change` event re-dispatched from useBtn.click lets
        // Livewire's own wire:model handler pick it up normally.
        document.addEventListener('change', function (e) {
            if (e.target !== fileInput) return;
            // Skip when the dispatcher is our own re-trigger — the
            // trimmed Blob was just written back, don't re-prompt.
            if (fileInput.dataset.trimReentry === '1') {
                fileInput.dataset.trimReentry = '';
                return;
            }
            var files = e.target.files;
            if (!files || !files.length) return;
            for (var i = 0; i < files.length; i++) {
                var f = files[i];
                if (f && f.type && f.type.indexOf('video/') === 0) {
                    e.stopImmediatePropagation();
                    e.preventDefault();
                    var others = [];
                    for (var j = 0; j < files.length; j++) if (j !== i) others.push(files[j]);
                    openModal(f, others);
                    return;
                }
            }
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
