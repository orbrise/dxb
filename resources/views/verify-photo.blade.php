@extends('components.layouts.app-evoory')

@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
/* Evoory Theme - Verify Photo Page */
body { background: #0a0a0a !important; }

#cap {
    width: 100%;
    max-width: 100%;
    height: auto;
    background: #000;
}
#verificationCameraModal {
    display: none !important;
    pointer-events: none;
}
#verificationCameraModal.show,
#verificationCameraModal.in {
    display: block !important;
    pointer-events: auto;
}
.hidden { display: none !important; }

/* Page header bar */
.ev-page-header {
    background: #131616;
    padding: 14px 0;
    border-bottom: 1px solid #2a2a2a;
}
.ev-page-header .ev-container {
    display: flex;
    align-items: center;
    gap: 20px;
}
.ev-back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
}
.ev-back-link:hover {
    color: #d4f84d;
    text-decoration: none;
}
.ev-page-title {
    color: #fff;
    font-size: 18px;
    font-weight: 400;
    margin: 0;
    flex: 1;
    text-align: center;
}

/* Two column layout */
.verify-layout {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}
.verify-left {
    flex: 1;
    min-width: 0;
}
.verify-right {
    flex: 1;
    min-width: 0;
}

/* Left column card */
.verify-card {
    background: #111;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 24px;
}

/* Step list styling */
.verify-steps {
    list-style: none;
    padding: 0;
    margin: 0;
}
.verify-steps li {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 24px;
    padding-bottom: 24px;
    border-bottom: 1px solid #1a1a1a;
}
.verify-steps li:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.step-number {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    background: transparent;
    border: 2px solid #C1F11D;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #C1F11D;
    font-weight: 700;
    font-size: 14px;
}
.step-content {
    color: #fff;
    font-size: 14px;
    line-height: 1.6;
}
.step-content code {
    background: #1a1a1a;
    border: 1px solid #C1F11D;
    color: #C1F11D;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 15px;
    font-weight: 700;
}

/* Profile photo in step 1 */
.verify-profile-photo {
    margin-top: 10px;
    border-radius: 6px;
    overflow: hidden;
    display: inline-block;
    border: 1px solid #2a2a2a;
}
.verify-profile-photo img {
    display: block;
    max-width: 100px;
    height: auto;
}

/* Open Camera button */
.btn-open-camera {
    background: #C1F11D;
    color: #000;
    border: none;
    border-radius: 24px;
    padding: 10px 28px;
    font-size: 15px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.btn-open-camera:hover {
    background: #d4f84d;
    color: #000;
}

/* QR code section */
.qr-section {
    text-align: center;
    margin-top: 20px;
}
.qr-section p {
    color: #999;
    font-size: 13px;
    margin-bottom: 10px;
}
.qr-code-box {
    display: inline-block;
    background: #fff;
    padding: 8px;
    border-radius: 6px;
}

/* Info alert */
.verify-info-alert {
    background: rgba(193, 241, 29, 0.06);
    border: 1px solid rgba(193, 241, 29, 0.15);
    border-radius: 8px;
    padding: 12px 16px;
    color: #aaa;
    font-size: 13px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    margin-top: 20px;
}
.verify-info-alert i {
    color: #C1F11D;
    font-size: 14px;
    margin-right: 4px;
}
.verify-info-alert .copy-link {
    color: #C1F11D;
    text-decoration: none;
    font-weight: 600;
    border: none;
    background: none;
    padding: 0;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}
.verify-info-alert .copy-link:hover {
    color: #d4f84d;
}

/* Warning alert */
.verify-warning {
    background: #DDDAC8;
    border-radius: 5px;
    padding: 16px 20px;
    color: #896C14;
    font-size: 14px;
    line-height: 1.2;
    margin-bottom: 24px;
    font-weight: 500;
}
.verify-warning b {
    color: #fff;
}

/* Why verify section */
.verify-pitch h2 {
    color: #fff;
    font-size: 22px;
    font-weight: 400;
    margin-bottom: 20px;
}
.verify-pitch h3 {
    color: #fff;
    font-size: 16px;
    font-weight: 400;
    margin-bottom: 4px;
}
.verify-pitch p {
    color: #ffffff;
    font-size: 14px;
    margin-bottom: 30px;
    margin-top:0px;
}

/* Profile selector */
.profile-selector {
    background: transparent;
    border: none;
    padding: 0;
    margin-bottom: 20px;
    position: relative;
}
.profile-selector h4 {
    color: #C1F11D;
    font-weight: normal;
    font-size: 16px;
    white-space: nowrap;
    margin: 0;
}
.profile-selector .selector-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.profile-search-wrapper {
    position: relative;
    flex: 1;
    max-width: 400px;
}
.profile-search-wrapper .search-group {
    display: flex;
}
#profileResults {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 8px;
    margin-top: 5px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.5);
}
#profileResults.show {
    display: block;
}
.profile-option {
    padding: 6px 12px;
    cursor: pointer;
    background: #1a1a1a;
    transition: all 0.2s;
    color: #fff;
    border-bottom: 1px solid #2a2a2a;
}
.profile-option:last-child {
    border-bottom: none;
}
.profile-option:hover {
    background: #222;
}
.profile-option.selected {
    background: #C1F11D;
    color: #000;
    font-weight: 500;
}
.profile-option .opt-row {
    display: flex;
    align-items: center;
}
.profile-thumb {
    width: 40px;
    height: 40px;
    object-fit: cover;
    margin-right: 10px;
}
#profileSearch {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    color: #fff;
    padding: 6px 12px;
    font-size: 14px;
    border-radius: 6px 0 0 6px;
    outline: none;
    flex: 1;
    min-width: 0;
}
#profileSearch:focus {
    background: #222;
    border-color: #C1F11D;
}
#profileSearch::placeholder {
    color: #666;
}
#searchBtn {
    background: #C1F11D;
    border: 1px solid #C1F11D;
    color: #000;
    padding: 6px 12px;
    border-radius: 0 6px 6px 0;
    cursor: pointer;
    font-size: 14px;
}
#searchBtn:hover {
    background: #d4f84d;
}

/* Modal overrides for dark theme */
.modal-backdrop { background: rgba(0,0,0,0.7); }
.modal-backdrop.show, .modal-backdrop.in { opacity: 1; }
#verificationCameraModal .modal-dialog {
    max-width: 600px;
    margin: 30px auto;
}
#verificationCameraModal .modal-content {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    color: #fff;
    border-radius: 8px;
    overflow: hidden;
    padding: 0;
}
#verificationCameraModal .modal-header {
    border-bottom: 1px solid #2a2a2a;
    background: #111;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
#verificationCameraModal .modal-title {
    color: #fff;
    font-size: 15px;
    margin: 0;
}
#verificationCameraModal .modal-title code {
    background: #1a1a1a;
    border: 1px solid #C1F11D;
    color: #C1F11D;
    padding: 2px 8px;
    border-radius: 4px;
}
#verificationCameraModal .close {
    color: #fff;
    opacity: 0.8;
    font-size: 24px;
    background: none;
    border: none;
    cursor: pointer;
}
#verificationCameraModal .modal-body {
    padding: 0;
    position: relative;
}
#verificationCameraModal .btn-success,
#verificationCameraModal .btn-primary {
    background: #C1F11D;
    color: #000;
    border: none;
    font-weight: 600;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
}
#verificationCameraModal .btn-primary:disabled {
    background: #333;
    color: #666;
    cursor: not-allowed;
}
#verificationCameraModal .btn-default {
    background: #2a2a2a;
    color: #fff;
    border: 1px solid #444;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
}
#verificationCameraModal .modal-footer-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 16px;
    border-top: 1px solid #2a2a2a;
}
#verificationCameraModal .modal-warning {
    padding: 8px 16px;
    color: #999;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Verify info alert */
.verify-info-alert {
    background: rgba(47, 128, 137, 0.15);
    border: 1px solid rgba(47, 128, 137, 0.35);
    border-radius: 10px;
    padding: 14px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 16px;
    max-width: 420px;
    width: 100%;
}
.verify-info-alert > .fa-info-circle {
    color: #5bb8c4;
    font-size: 16px;
    flex-shrink: 0;
}
.verify-info-alert > span {
    color: #8ec8cf;
    font-size: 13px;
    line-height: 1.4;
}
.verify-info-alert .copy-link {
    background: none;
    border: none;
    color: #5bb8c4;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 0;
    white-space: nowrap;
    font-size: 13px;
    flex-shrink: 0;
}
.verify-info-alert .copy-link:hover {
    color: #7dd8e4;
}
.verify-info-alert .copy-link .fa-arrow-right {
    font-size: 12px;
}

@media (max-width: 768px) {
    .verify-layout {
        flex-direction: column;
    }
    .ev-page-title {
        font-size: 15px;
        text-align: left;
    }
    .verify-card {
        padding: 16px;
    }
    .profile-search-wrapper {
        max-width: 100%;
    }
    .verify-info-alert {
        flex-wrap: wrap;
        max-width: 100%;
    }
}
</style>
@endpush

@section('content')
{{-- Page Sub-header --}}
<div class="ev-page-header">
    <div class="ev-container">
        <a class="ev-back-link" href="/my-profile/{{ $user->slug }}/{{ $user->id }}">
            <i class="fas fa-chevron-left"></i>
            My profile
        </a>
        <h1 class="ev-page-title">Verification Application for {{ $user->name }}</h1>
    </div>
</div>

<div class="ev-container" style="padding-top: 30px; padding-bottom: 60px;">
    {{-- Profile Selector --}}
    <div class="profile-selector">
        <div class="selector-row">
            <h4><i class="fas fa-user-circle" style="margin-right:6px"></i>Select Profile to Verify</h4>
            <div class="profile-search-wrapper">
                <div class="search-group">
                    <input type="text"
                           id="profileSearch"
                           placeholder="Type profile name or ID to search..."
                           autocomplete="off">
                    <button type="button" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <div id="profileResults"></div>
            </div>
        </div>
    </div>

    <div class="verify-layout">
        {{-- Left Column --}}
        <div class="verify-left">
            <div class="verify-card">
                <ol class="verify-steps">
                    <li style="margin-bottom: 0px; padding-bottom: 0px;">
                        <span class="step-number">1</span>
                        <span class="step-content">The photos on the listing must be yours:
                        </span>
                    </li>
                   <div class="verify-profile-photo" style="margin-bottom: 24px;    margin-left: 3rem;">
                                @if(!empty($user->singleimg->image))
                                    <img alt="{{ $user->name }}" src="{{ smart_asset("userimages/".$user->user_id."/".$user->id."/".$user->singleimg->image) }}" width="100" />
                                @endif
                            </div>
                    <li>
                        <span class="step-number">2</span>
                        <span class="step-content">We must be able to match them with the new photo you upload.</span>
                    </li>
                    <li>
                        <span class="step-number">3</span>
                        <span class="step-content">Write <code>{{ $photo_code }}</code> on a piece of paper and take a new photo of yourself <i>holding that piece of paper</i>.</span>
                    </li>
                    <li>
                        <span class="step-number">4</span>
                        <span class="step-content">Face not shown in profile photos? Take a verification photo showing the same outfit and visible body features.</span>
                    </li>
                </ol>

                <form class="simple_form validate" id="new_verification_request" novalidate="novalidate" action="/my-profile/{{ $user->slug }}/{{ $user->id }}/verification_request" accept-charset="UTF-8" method="post">
                    @csrf
                    <input type="hidden" id="photo_code" name="photo_code" value="{{ $photo_code }}" />
                    <input name="utf8" type="hidden" value="&#x2713;" />
                    <div class="photo">
                        <div style="display:none">
                            <input id="img_frame" type="hidden" value="" name="verification_request[verification_request_comments_attributes][0][photo]" />
                            <input id="img_grid" type="hidden" value="" name="verification_request[verification_request_comments_attributes][0][grid]" />
                            <input id="description" type="hidden" name="verification_request[verification_request_comments_attributes][0][content]" />
                        </div>
                    </div>
                    <div style="padding-top: 16px;">
                        <div style="display:flex;flex-direction:column;align-items:center;">
                            <button type="button" class="btn-open-camera" id="openVerificationCamera">
                                <i class="fas fa-spinner fa-spin" style="display:none !important;" id="cameraSpinner"></i>
                                <i class="fas fa-video" id="cameraIcon"></i>
                                Open Camera
                            </button>
                        </div>

                        {{-- Camera Modal --}}
                        <div class="modal fade" id="verificationCameraModal" role="dialog" tabindex="-1" aria-hidden="true" style="display: none;">
                            <div class="modal-dialog" data-timer="3" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">
                                            Hold a piece of paper with the following code:
                                            <code><b>{{ $photo_code }}</b></code>
                                        </h4>
                                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <button class="btn btn-circle" data-btn-change=""
                                                style="position:absolute;top:1rem;left:1rem;display:flex;align-items:center;z-index:1;padding:4px;background:rgba(0,0,0,0.5);border:none;border-radius:50%;cursor:pointer"
                                                title="Flip camera" type="button">
                                            <svg width="28" height="28" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38.35 28">
                                                <path d="M14.83,25.6c-3.86-.37-7.28-1.22-9.83-2.4-3.12-1.43-5-3.35-5-5.5s2.02-4.21,5.34-5.65v2.01c-1.97,1.05-3.13,2.31-3.13,3.65,0,1.5,1.48,2.92,3.92,4.04,2.25,1.03,5.26,1.79,8.71,2.14v-2.26l.15.1,2.33,1.52,2.33,1.52.08.05-.05.03-.03.02-2.33,1.52-2.33,1.52-.15.1v-2.4h0Z" fill="#fff" fill-rule="evenodd"/>
                                            </svg>
                                        </button>
                                        <div class="capture-wrapper">
                                            <video autoplay="" id="verificationVideo" playsInline="true" style="width:100%;background:#000"></video>
                                            <div class="hidden" id="verificationPreview" style="position:absolute;top:0;left:0;right:0;bottom:0"></div>
                                        </div>
                                    </div>
                                    <div class="modal-footer-row">
                                        <button class="btn btn-default hidden" data-reset-frame="" type="button">Try again</button>
                                        <button class="btn btn-success" id="captureVerificationBtn" style="display:inline-flex;align-items:center" type="button">
                                            <svg style="margin-right:6px" xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20"><path fill="currentColor" d="M206.309-108.001q-41.033 0-69.67-28.638-28.638-28.637-28.638-69.67V-358H194v151.691q0 4.616 3.846 8.463 3.847 3.846 8.463 3.846H358v85.999H206.309Zm395.691 0V-194h151.691q4.616 0 8.463-3.846 3.846-3.847 3.846-8.463V-358h85.999v151.691q0 41.033-28.638 69.67-28.637 28.638-69.67 28.638H602ZM108.001-602v-151.691q0-41.033 28.638-69.67 28.637-28.638 69.67-28.638H358V-766H206.309q-4.616 0-8.463 3.846-3.846 3.847-3.846 8.463V-602h-85.999ZM766-602v-151.691q0-4.616-3.846-8.463-3.847-3.846-8.463-3.846H602v-85.999h151.691q41.033 0 69.67 28.638 28.638 28.637 28.638 69.67V-602H766ZM480-272.001q-86.542 0-147.271-60.728Q272.001-393.458 272.001-480q0-86.542 60.728-147.271Q393.458-687.999 480-687.999q86.542 0 147.271 60.728Q687.999-566.542 687.999-480q0 86.542-60.728 147.271Q566.542-272.001 480-272.001Zm.118-85.999Q531-358 566.5-393.618q35.5-35.617 35.5-86.5Q602-531 566.382-566.5q-35.617-35.5-86.5-35.5Q429-602 393.5-566.382q-35.5 35.617-35.5 86.5Q358-429 393.618-393.5q35.617 35.5 86.5 35.5ZM480-480Z"></path></svg>
                                            <span>Take Photo</span>
                                        </button>
                                        <button class="btn btn-primary" id="submitVerificationBtn" disabled="" type="submit">Submit for verification</button>
                                    </div>
                                    <div class="modal-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <span>The image you upload will <b>never</b> be published or shared.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <canvas class="hidden" id="frame_capture"></canvas>
                        <canvas class="hidden" id="canvas_ops"></canvas>
                        <canvas class="hidden" id="canvas_ops_ready"></canvas>
                        <canvas class="hidden" id="offscreen"></canvas>
                    </div>
                </form>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="verify-right">
            <div class="verify-warning">
                We do accept passport or ID scans or photos that are not clearly of the same person as on the listing.
                Any attempt to use different person's photos after verification will result in a verification ban.
            </div>
            <div class="verify-pitch">
                <h2>Why verify your photos?</h2>
                <div>
                    <h3>Priority</h3>
                    <p>Listings with verified photos are shown ahead of non-verified ones</p>
                </div>
                <div>
                    <h3>Trust</h3>
                    <p>Users value real photos, and thus are more likely to contact you</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    window.initVerificationPage = function() {
    const spinnerEl = document.getElementById('cameraSpinner');
    const cameraIconEl = document.getElementById('cameraIcon');
    if (spinnerEl) spinnerEl.style.display = 'none';
    if (cameraIconEl) cameraIconEl.style.display = 'inline-block';

    const openBtn = document.getElementById('openVerificationCamera');
    const modalEl = document.getElementById('verificationCameraModal');
    const video = document.getElementById('verificationVideo');
    const captureBtn = document.getElementById('captureVerificationBtn');
    const submitBtn = document.getElementById('submitVerificationBtn');
    const preview = document.getElementById('verificationPreview');
    const canvas = document.createElement('canvas');
    let stream = null;

    if (!openBtn) return;

    const newOpenBtn = openBtn.cloneNode(true);
    openBtn.parentNode.replaceChild(newOpenBtn, openBtn);

    const freshOpenBtn = document.getElementById('openVerificationCamera');
    const freshSpinner = document.getElementById('cameraSpinner');
    const freshCameraIcon = document.getElementById('cameraIcon');

    if (freshSpinner) freshSpinner.style.display = 'none';
    if (freshCameraIcon) freshCameraIcon.style.display = 'inline-block';

    function showModal() {
        modalEl.style.display = 'block';
        modalEl.classList.add('show', 'in');
        document.body.classList.add('modal-open');

        let backdrop = document.querySelector('.modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show in';
            document.body.appendChild(backdrop);
        }
    }

    function hideModal() {
        modalEl.style.display = 'none';
        modalEl.classList.remove('show', 'in');
        document.body.classList.remove('modal-open');

        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();

        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }

        video.style.display = '';
        captureBtn.style.display = '';
        preview.classList.add('hidden');
        preview.innerHTML = '';
        submitBtn.disabled = true;
        delete submitBtn.dataset.imageData;
    }

    freshOpenBtn.onclick = async () => {
        try {
            freshSpinner.style.display = 'inline-block';
            freshCameraIcon.style.display = 'none';

            stream = await navigator.mediaDevices.getUserMedia({
                video: { width: 1280, height: 720 }
            });
            video.srcObject = stream;
            await video.play();

            freshSpinner.style.display = 'none';
            freshCameraIcon.style.display = 'inline-block';

            showModal();
        } catch (err) {
            console.error('Camera error:', err);
            freshSpinner.style.display = 'none';
            freshCameraIcon.style.display = 'inline-block';
            alert('Could not access camera. Please ensure camera permissions are granted.');
        }
    };

    modalEl.querySelector('[data-dismiss="modal"]').onclick = function() {
        hideModal();
    };

    modalEl.onclick = function(e) {
        if (e.target === modalEl) hideModal();
    };

    captureBtn.onclick = () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);

        const imageData = canvas.toDataURL('image/jpeg', 1.0);

        preview.innerHTML = `<img src="${imageData}" style="width:100%;height:100%;object-fit:contain">`;
        preview.classList.remove('hidden');

        video.style.display = 'none';
        captureBtn.style.display = 'none';
        submitBtn.removeAttribute('disabled');
        submitBtn.dataset.imageData = imageData;
    };

    submitBtn.onclick = () => {
        const imageData = submitBtn.dataset.imageData;
        if (!imageData) { alert('No image captured!'); return; }

        const formData = new FormData();
        formData.append('photoData', imageData);
        formData.append('_token', '{{ csrf_token() }}');

        const slug = '{{ $user->slug }}';
        const id = '{{ $user->id }}';

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

        fetch(`/my-profile/${slug}/${id}/verify-photo`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Upload failed');
            return response.text();
        })
        .then(data => {
            hideModal();
            window.location.href = `/my-profile/${slug}/${id}?verification_success=1`;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to upload photo. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Photo';
        });
    };

    // Copy verification URL
    const copyBtn = document.querySelector('[data-copy-btn]');
    if (copyBtn) {
        copyBtn.onclick = function() {
            const target = document.querySelector(this.getAttribute('data-copy-btn'));
            if (target) {
                navigator.clipboard.writeText(target.textContent.trim()).then(() => {
                    const span = this.querySelector('[data-text]');
                    if (span) {
                        const original = span.textContent;
                        span.textContent = span.getAttribute('data-text');
                        setTimeout(() => { span.textContent = original; }, 2000);
                    }
                });
            }
        };
    }

    // Profile Search and Selection
    let selectedProfileId = {{ $user->id }};
    const profileSearch = document.getElementById('profileSearch');
    const profileResults = document.getElementById('profileResults');
    const searchBtn = document.getElementById('searchBtn');
    const assetBaseUrl = 'https://assets.massagerepublic.com.co/';

    if (!profileSearch) return;

    function searchProfiles(query) {
        fetch('/api/search-my-profiles?q=' + encodeURIComponent(query), {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Search failed');
            return response.json();
        })
        .then(data => {
            displayProfiles(data.profiles);
        })
        .catch(error => {
            console.error('Search error:', error);
            profileResults.innerHTML = '<div style="padding:12px;color:#666">Error loading profiles</div>';
            profileResults.classList.add('show');
        });
    }

    function displayProfiles(profiles) {
        profileResults.innerHTML = '';

        if (!profiles || profiles.length === 0) {
            profileResults.innerHTML = '<div style="padding:12px;color:#666">No profiles found</div>';
            profileResults.classList.add('show');
            return;
        }

        profiles.forEach(function(profile) {
            const isSelected = profile.id == selectedProfileId ? 'selected' : '';
            const isCurrent = profile.id == {{ $user->id }} ? ' <span style="color:#C1F11D">(Current)</span>' : '';

            let imageUrl = '/assets/images/default-avatar.png';
            if (profile.cover_image) {
                imageUrl = assetBaseUrl + 'userimages/' + profile.user_id + '/' + profile.id + '/' + profile.cover_image;
            } else if (profile.single_image) {
                imageUrl = assetBaseUrl + 'userimages/' + profile.user_id + '/' + profile.id + '/' + profile.single_image;
            }

            const div = document.createElement('div');
            div.className = 'profile-option ' + isSelected;
            div.setAttribute('data-profile-id', profile.id);
            div.setAttribute('data-slug', profile.slug);
            div.innerHTML = '<div class="opt-row">' +
                '<img src="' + imageUrl + '" class="profile-thumb" alt="' + profile.name + '" onerror="this.src=\'/assets/images/default-avatar.png\'">' +
                '<div><strong>' + profile.name + '</strong>' + isCurrent +
                '<br><small style="color:#aaa">ID: ' + profile.id + '</small></div></div>';

            div.addEventListener('click', function() {
                window.location.href = '/my-profile/' + this.getAttribute('data-slug') + '/' + this.getAttribute('data-profile-id') + '/verify-photo';
            });

            profileResults.appendChild(div);
        });

        profileResults.classList.add('show');
    }

    let searchTimeout;
    profileSearch.addEventListener('input', function() {
        const query = this.value.trim();
        clearTimeout(searchTimeout);
        if (query.length === 0) {
            profileResults.classList.remove('show');
            profileResults.innerHTML = '';
            return;
        }
        searchTimeout = setTimeout(function() { searchProfiles(query); }, 300);
    });

    searchBtn.addEventListener('click', function() {
        const query = profileSearch.value.trim();
        if (query.length > 0) searchProfiles(query);
    });

    profileSearch.addEventListener('keypress', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length > 0) searchProfiles(query);
        }
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.profile-search-wrapper')) {
            profileResults.classList.remove('show');
        }
    });
}

document.addEventListener('DOMContentLoaded', window.initVerificationPage);
document.addEventListener('livewire:navigated', window.initVerificationPage);
document.addEventListener('livewire:init', window.initVerificationPage);

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    window.initVerificationPage();
}
</script>
@endpush

<script>
    (function() {
        function tryInit() {
            if (typeof window.initVerificationPage === 'function') {
                window.initVerificationPage();
            } else {
                setTimeout(tryInit, 50);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryInit);
        } else {
            setTimeout(tryInit, 10);
        }
    })();
</script>