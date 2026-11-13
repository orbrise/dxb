{{-- Profile video thumbnail capturer. Walks every .profile-video-preview
     element (freshly rendered after a Livewire morph), grabs a non-black frame
     about 1 s in (grabFrame), converts it to a JPEG data URL, and ships it back to
     the Livewire component via `setVideoThumbnail` so the server can decode +
     store it beside the MP4.

     Shared between new-profile.blade and user-profile.blade — @once guards
     against the double-emit case where a page accidentally includes it
     twice (e.g. from a parent + a child partial).

     De-dup key is the video's SRC URL stored in a module-level Set, NOT a
     dataset attribute on the element. Reason: Livewire morph can replace
     the <video> node when $tempVideos re-renders (there's no wire:key on
     the preview tiles), which wipes any data-* attributes. If we tracked
     "already captured" on the element, the fresh element would re-capture,
     dispatch setVideoThumbnail, trigger another morph, which creates a new
     element, which re-captures again — an infinite render loop that
     disables every form field on the page every ~1-2s. The URL set
     survives morphs and gives us a stable identity per clip. --}}
@once
<script>
(function () {
    if (window.__profileVideoThumbnailerWired) return;
    window.__profileVideoThumbnailerWired = true;

    const MAX_W = 720; // scale down big portrait clips so the JPEG stays small
    const MAX_H = 720;

    // Stable dedup set across Livewire morphs. Keyed on the URL's path (the
    // temp file): a re-render can sign the same file's preview URL afresh.
    const capturedSrcs = new Set();
    const keyOf = (src) => { try { return new URL(src, location.href).pathname; } catch (e) { return src; } };

    // Thumb overlays keyed by data-video-index. Needed because the <video>'s
    // `poster` attribute only shows BEFORE the browser loads any video
    // content. With preload="metadata" on the preview tile, the first frame
    // loads a moment later and REPLACES the poster — which for a trimmed
    // webm is the black warm-up frame from MediaRecorder, so the tile goes
    // black after ~1-2s. The overlay is a plain <img> absolutely positioned
    // on top of the video; it never gets replaced by anything the video
    // element does. We keep the index→dataUrl map so the overlay can be
    // re-attached after every Livewire morph (morph wipes the img sibling
    // along with any data-* flag we put on the parent).
    const indexToThumb = new Map(); // String(data-video-index) → dataUrl

    function applyOverlay(video, dataUrl) {
        if (!video || !dataUrl) return;
        const parent = video.parentElement;
        if (!parent) return;
        // Guarantee the overlay has an anchor to position against — the
        // Blade already sets position:relative on the record tile, but
        // defensive-set here in case a different caller forgets.
        if (getComputedStyle(parent).position === 'static') {
            parent.style.position = 'relative';
        }
        let overlay = parent.querySelector('img.user-video-thumb-overlay');
        if (!overlay) {
            overlay = document.createElement('img');
            overlay.className = 'user-video-thumb-overlay';
            overlay.alt = '';
            overlay.setAttribute('aria-hidden', 'true');
            overlay.style.cssText =
                'position:absolute;inset:0;width:100%;height:100%;' +
                'object-fit:cover;pointer-events:none;z-index:1;' +
                'display:block;background:#000;';
            // Insert BEFORE the play-glyph span (last child of the tile)
            // so the glyph still sits on top of our overlay.
            parent.insertBefore(overlay, video.nextSibling);
        }
        if (overlay.src !== dataUrl) overlay.src = dataUrl;
    }

    function sweepOverlays() {
        if (indexToThumb.size === 0) return;
        document.querySelectorAll('video.profile-video-preview').forEach(v => {
            const idx = v.getAttribute('data-video-index');
            if (idx === null) return;
            const d = indexToThumb.get(String(idx));
            if (d) applyOverlay(v, d);
        });
    }

    // True when a canvas is (nearly) all black. Same test as the server's
    // ProfileVideoThumbnail::isBlank().
    function isBlank(canvas) {
        const probe = document.createElement('canvas');
        probe.width = probe.height = 24;
        const context = probe.getContext('2d');
        context.drawImage(canvas, 0, 0, 24, 24);
        const px = context.getImageData(0, 0, 24, 24).data;
        for (let i = 0; i < px.length; i += 4) {
            if (px[i] + px[i + 1] + px[i + 2] >= 60) return false;
        }
        return true;
    }

    // A frame of the clip as a JPEG data URL, or null. Drawing the preview
    // tile as soon as its size was known saved a black JPEG for every upload:
    // no frame is decoded at that point, and clips often open on black. So
    // load a hidden muted copy, seek a moment in, draw once that frame has
    // decoded ('seeked'), and move further along while frames are black.
    function grabFrame(src) {
        return new Promise((resolve) => {
            const video = document.createElement('video');
            video.muted = true;
            video.playsInline = true;
            video.preload = 'auto';
            let times = [];
            let done = false;
            const finish = (dataUrl) => {
                if (done) return;
                done = true;
                clearTimeout(timer);
                video.removeAttribute('src');
                video.load();
                resolve(dataUrl);
            };
            const timer = setTimeout(() => finish(null), 15000);
            const next = () => times.length ? (video.currentTime = times.shift()) : finish(null);

            video.addEventListener('loadedmetadata', () => {
                const d = Number.isFinite(video.duration) ? video.duration : 0;
                // 1 s in (a third of a very short clip), then further along.
                // MediaRecorder webms report no duration: just try 1 s and 0.1 s.
                times = (d > 0 ? [Math.min(1, d / 3), d * 0.25, d * 0.5, d * 0.75] : [1, 0.1])
                    .map(t => d > 0 ? Math.max(0, Math.min(t, d - 0.05)) : t)
                    .filter((t, i, all) => all.indexOf(t) === i);
                next();
            }, { once: true });
            video.addEventListener('seeked', () => {
                try {
                    const vw = video.videoWidth, vh = video.videoHeight;
                    if (!vw || !vh) return next();
                    const scale = Math.min(1, MAX_W / vw, MAX_H / vh);
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.max(1, Math.round(vw * scale));
                    canvas.height = Math.max(1, Math.round(vh * scale));
                    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                    if (isBlank(canvas)) return next();
                    finish(canvas.toDataURL('image/jpeg', 0.78));
                } catch (err) {
                    // Decode / tainted-canvas failure: no browser thumbnail.
                    console.warn('[profileVideoThumbnailer] capture failed', err);
                    finish(null);
                }
            });
            video.addEventListener('error', () => finish(null), { once: true });
            video.src = src;
        });
    }

    async function captureOne(video) {
        if (!video) return;
        const src = video.currentSrc || video.src || '';
        if (!src || capturedSrcs.has(keyOf(src))) return;
        const idx = video.getAttribute('data-video-index');
        if (idx === null) return;

        // Prefer a user-captured thumb (from the trim modal's "Capture frame"
        // button) over a canvas-grab of the first frame. The trim modal pushes
        // data URLs onto window.__pendingVideoThumbs as a FIFO queue; videos
        // uploaded via the modal land here in the same order they were
        // confirmed, so popping the head gives us the matching thumb. Falls
        // back to the canvas path when the queue is empty (regular video
        // upload with no trim-modal pick).
        //
        // Why this matters: MediaRecorder's trimmed webm starts with 2-3 black
        // warm-up frames, so the canvas grab is a black rectangle. The user's
        // captured frame is the actual moment they picked in the trim UI.
        const q = window.__pendingVideoThumbs;
        if (q && q.length) {
            const dataUrl = q.shift();
            capturedSrcs.add(keyOf(src));
            indexToThumb.set(String(idx), dataUrl);
            applyOverlay(video, dataUrl);
            // Set poster too for completeness (helps if the overlay is
            // ever removed by future refactors).
            try { video.setAttribute('poster', dataUrl); } catch (e) {}
            if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                window.Livewire.dispatch('setVideoThumbnail', { index: Number(idx), dataUrl });
            }
            return;
        }

        // Mark captured BEFORE the (async) grab so the morphs that happen
        // meanwhile can't start a second capture of the same clip. On
        // failure it stays marked: retrying every morph never helps, and the
        // server renders a thumbnail with ffmpeg when none arrives.
        const key = keyOf(src);
        capturedSrcs.add(key);
        const dataUrl = await grabFrame(src);
        if (!dataUrl) return;

        // Removing a clip meanwhile re-numbers the tiles: find this clip's
        // index again (or drop the frame if it was removed).
        const tile = Array.from(document.querySelectorAll('video.profile-video-preview'))
            .find(v => keyOf(v.currentSrc || v.src || '') === key);
        const index = tile?.getAttribute('data-video-index');
        if (index === null || index === undefined) return;
        indexToThumb.set(String(index), dataUrl);
        applyOverlay(tile, dataUrl);
        if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
            window.Livewire.dispatch('setVideoThumbnail', { index: Number(index), dataUrl });
        }
    }

    function captureAll() {
        // Re-attach overlays FIRST so any newly-morphed video tile shows the
        // user's thumb immediately, before the capture of new clips runs.
        sweepOverlays();

        document.querySelectorAll('video.profile-video-preview').forEach(v => {
            const src = v.currentSrc || v.src || '';
            if (src && !capturedSrcs.has(keyOf(src))) captureOne(v);
        });
    }

    document.addEventListener('DOMContentLoaded', captureAll);
    document.addEventListener('livewire:navigated', captureAll);
    document.addEventListener('livewire:initialized', () => {
        captureAll();
        if (window.Livewire && window.Livewire.hook) {
            window.Livewire.hook('morph.updated', captureAll);
        }
    });
})();
</script>
@endonce
