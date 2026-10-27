{{-- Profile video thumbnail capturer. Walks every .profile-video-preview
     element (freshly rendered after a Livewire morph), draws its first frame
     into a hidden canvas, converts to a JPEG data URL, and ships it back to
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

    // Stable dedup set across Livewire morphs. Keys are object-URL strings
    // (same URL = same temp file = don't re-capture).
    const capturedSrcs = new Set();

    function captureOne(video) {
        if (!video) return;
        const src = video.currentSrc || video.src || '';
        if (!src || capturedSrcs.has(src)) return;
        const idx = video.getAttribute('data-video-index');
        if (idx === null) return;

        try {
            const vw = video.videoWidth, vh = video.videoHeight;
            if (!vw || !vh) return; // metadata not ready yet; loadedmetadata will retry
            const scale = Math.min(1, MAX_W / vw, MAX_H / vh);
            const tw = Math.max(1, Math.round(vw * scale));
            const th = Math.max(1, Math.round(vh * scale));
            const canvas = document.createElement('canvas');
            canvas.width = tw;
            canvas.height = th;
            canvas.getContext('2d').drawImage(video, 0, 0, tw, th);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.78);
            // Mark captured BEFORE dispatching so Livewire's resulting morph
            // can't race us into a second capture of the same clip.
            capturedSrcs.add(src);
            if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                window.Livewire.dispatch('setVideoThumbnail', { index: Number(idx), dataUrl });
            }
        } catch (err) {
            // CORS / decode failure — mark captured anyway so we don't keep
            // retrying every morph. Browser's <video> poster-less fallback
            // kicks in on display.
            console.warn('[profileVideoThumbnailer] capture failed', err);
            capturedSrcs.add(src);
        }
    }

    function captureAll() {
        document.querySelectorAll('video.profile-video-preview').forEach(v => {
            const src = v.currentSrc || v.src || '';
            if (src && capturedSrcs.has(src)) return;
            if (v.readyState >= 1 /* HAVE_METADATA */ && v.videoWidth) {
                captureOne(v);
            } else {
                v.addEventListener('loadedmetadata', () => captureOne(v), { once: true });
                v.addEventListener('loadeddata',     () => captureOne(v), { once: true });
            }
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
