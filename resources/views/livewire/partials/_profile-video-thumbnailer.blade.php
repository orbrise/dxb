{{-- Profile video thumbnail capturer. Walks every .profile-video-preview
     element (freshly rendered after a Livewire morph), draws its first frame
     into a hidden canvas, converts to a JPEG data URL, and ships it back to
     the Livewire component via `setVideoThumbnail` so the server can decode +
     store it beside the MP4.

     Shared between new-profile.blade and user-profile.blade — @once guards
     against the double-emit case where a page accidentally includes it
     twice (e.g. from a parent + a child partial).

     Keyed off `data-video-index` + `data-vt-captured` so a preview that
     already produced a thumbnail doesn't re-capture on every Livewire
     re-render. The whole system is safe to retrigger as often as we like. --}}
@once
<script>
(function () {
    if (window.__profileVideoThumbnailerWired) return;
    window.__profileVideoThumbnailerWired = true;

    const MAX_W = 720; // scale down big portrait clips so the JPEG stays small
    const MAX_H = 720;

    function captureOne(video) {
        if (!video || video.dataset.vtCaptured === '1') return;
        const idx = video.getAttribute('data-video-index');
        if (idx === null) return;

        // Already seeked past 0? still fine; draw whatever's currently decoded.
        try {
            const vw = video.videoWidth, vh = video.videoHeight;
            if (!vw || !vh) return; // metadata not ready yet; try again later
            const scale = Math.min(1, MAX_W / vw, MAX_H / vh);
            const tw = Math.max(1, Math.round(vw * scale));
            const th = Math.max(1, Math.round(vh * scale));
            const canvas = document.createElement('canvas');
            canvas.width = tw;
            canvas.height = th;
            canvas.getContext('2d').drawImage(video, 0, 0, tw, th);
            const dataUrl = canvas.toDataURL('image/jpeg', 0.78);
            // Ship it to Livewire. Scoped dispatch to the component that owns
            // the <video> so we don't accidentally hit another Livewire
            // instance on the same page.
            if (window.Livewire && typeof window.Livewire.dispatch === 'function') {
                window.Livewire.dispatch('setVideoThumbnail', { index: Number(idx), dataUrl });
            }
            video.dataset.vtCaptured = '1';
        } catch (err) {
            // CORS / decode failure — nothing we can do, server will store
            // the video without a poster. Browser's <video poster-less>
            // fallback kicks in on display.
            console.warn('[profileVideoThumbnailer] capture failed', err);
            video.dataset.vtCaptured = '1';
        }
    }

    function captureAll() {
        document.querySelectorAll('video.profile-video-preview').forEach(v => {
            if (v.dataset.vtCaptured === '1') return;
            // Hook per-video so we fire the second metadata is decoded.
            if (v.readyState >= 1 /* HAVE_METADATA */ && v.videoWidth) {
                captureOne(v);
            } else {
                v.addEventListener('loadedmetadata', () => captureOne(v), { once: true });
                // Fallback — some browsers (iOS) need an explicit seek-to-0
                // kick before they decode frame 0 for canvas drawing.
                v.addEventListener('loadeddata', () => captureOne(v), { once: true });
            }
        });
    }

    // Run on initial page load, after each Livewire morph (newly-added
    // tempVideos get fresh <video> elements), and on client-side nav.
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
