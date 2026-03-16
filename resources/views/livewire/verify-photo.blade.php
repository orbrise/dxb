@push('css')
<style>
/* Evoory Theme - Verify Photo Page */
.backclass { background: #000 !important; }
#header .nav-bar { background: #131616 !important; }
#header { margin-bottom: 0px !important; }
.content-wrapper, #content, .container-fluid { background: #0a0a0a !important; }

#cap {
    width: 100%;
    max-width: 100%;
    height: auto;
    background: #000;
}
.modal-body {
    padding: 0;
}
.video-container {
    position: relative;
    width: 100%;
    min-height: 300px;
    background: #000;
}

/* Step list styling */
.verify-steps {
    list-style: none;
    padding: 0;
    margin: 0;
}

.verify-steps li {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 28px;
}

.step-number {
    flex-shrink: 0;
    width: 36px;
    height: 36px;
    background: #1a1a1a;
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
    font-size: 15px;
    line-height: 1.5;
    padding-top: 6px;
}

.step-content code {
    background: #1a1a1a;
    border: 1px solid #C1F11D;
    color: #C1F11D;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 16px;
    font-weight: 700;
}

/* Profile photo in step 1 */
.verify-profile-photo {
    margin-top: 10px;
    border-radius: 8px;
    overflow: hidden;
    display: inline-block;
    border: 2px solid #2a2a2a;
}
.verify-profile-photo img {
    display: block;
    max-width: 115px;
    height: auto;
}

/* Open Camera button */
.btn-open-camera {
    background: #C1F11D;
    color: #000;
    border: none;
    border-radius: 24px;
    padding: 12px 32px;
    font-size: 16px;
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
    margin-top: 24px;
}
.qr-section p {
    color: #999;
    font-size: 14px;
    margin-bottom: 12px;
}
.qr-code-box {
    display: inline-block;
    background: #fff;
    padding: 8px;
    border-radius: 8px;
}

/* Info alert */
.verify-info-alert {
    background: #111;
    border: 1px solid #2a2a2a;
    border-radius: 8px;
    padding: 12px 16px;
    color: #aaa;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 24px;
}
.verify-info-alert i {
    color: #C1F11D;
    font-size: 16px;
}
.verify-info-alert .btn-link {
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
}
.verify-info-alert .btn-link:hover {
    color: #d4f84d;
}

/* Warning alert */
.verify-warning {
    background: rgba(193, 241, 29, 0.08);
    border: 1px solid rgba(193, 241, 29, 0.3);
    border-radius: 8px;
    padding: 16px 20px;
    color: #ccc;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 24px;
}
.verify-warning b {
    color: #fff;
}

/* Why verify section */
.verify-pitch {
    padding: 0;
}
.verify-pitch h2 {
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 20px;
}
.verify-pitch h3 {
    color: #C1F11D;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 4px;
}
.verify-pitch p {
    color: #999;
    font-size: 14px;
    margin-bottom: 16px;
}

/* Back link styling */
.ev-back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ev-back-link:hover {
    color: #d4f84d;
    text-decoration: none;
}

.ev-page-title {
    color: #fff;
    font-size: 20px;
    font-weight: 600;
    text-align: center;
    margin: 0;
}

.ev-header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #2a2a2a;
    margin-bottom: 30px;
}

/* Modal overrides for dark theme */
#camVerifyModal .modal-content {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    color: #fff;
}
#camVerifyModal .modal-header {
    border-bottom: 1px solid #2a2a2a;
    background: #111;
}
#camVerifyModal .modal-title {
    color: #fff;
}
#camVerifyModal .modal-title code {
    background: #1a1a1a;
    border: 1px solid #C1F11D;
    color: #C1F11D;
    padding: 2px 8px;
    border-radius: 4px;
}
#camVerifyModal .close {
    color: #fff;
    opacity: 0.8;
}
#camVerifyModal .btn-success {
    background: #C1F11D;
    color: #000;
    border: none;
    font-weight: 600;
}
#camVerifyModal .btn-primary {
    background: #C1F11D;
    color: #000;
    border: none;
    font-weight: 600;
}
#camVerifyModal .btn-primary:disabled {
    background: #333;
    color: #666;
}
#camVerifyModal .btn-default {
    background: #2a2a2a;
    color: #fff;
    border: 1px solid #444;
}
#camVerifyModal p {
    color: #999;
}

@media (max-width: 768px) {
    .ev-page-title {
        font-size: 16px;
        text-align: left;
    }
    .verify-steps li {
        margin-bottom: 20px;
    }
}
</style>
@endpush

@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/my-profile/{{ $user->slug }}/{{ $user->id }}">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">My profile</span>
      </a>
      <div class="title">
        <h1>
          <a href="">Verification Application for {{ $user->name }}</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

  <div class="container-fluid">
    <div class="content-wrapper no-sidebar">
      <div id="content">
        <div class="row">

          <div class="col-sm-6">
            <div class="block">
              <div class="verification-instructions">
                <ol class="verify-steps">
                  <li>
                    <span class="step-number">1</span>
                    <span class="step-content">The photos on the listing must be yours:
                      <div class="verify-profile-photo">
                        @if(!empty($user->singleimg->image))
                          <img alt="{{ $user->name }} - profile photo" class="img-responsive" src="{{ smart_asset("userimages/".$user->user_id."/".$user->singleimg->image) }}" width="115" />
                        @endif
                      </div>
                    </span>
                  </li>
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
              </div>
              <form class="simple_form validate" id="new_verification_request" novalidate="novalidate" action="/my-profile/{{ $user->slug }}/{{ $user->id }}/verification_request" accept-charset="UTF-8" method="post">
                @csrf
                <input type="hidden" id="photo_code" name="photo_code" value="{{ $photo_code }}" />
                <input name="utf8" type="hidden" value="&#x2713;" />
                <div class="photo">
                  <div class="form-group hidden verification_request_verification_request_comments_photo">
                    <input id="img_frame" class="hidden" type="hidden" value="" name="verification_request[verification_request_comments_attributes][0][photo]" />
                  </div>
                  <div class="form-group hidden verification_request_verification_request_comments_grid">
                    <input id="img_grid" class="hidden" type="hidden" value="" name="verification_request[verification_request_comments_attributes][0][grid]" />
                  </div>
                  <div class="form-group hidden verification_request_verification_request_comments_content">
                    <input id="description" class="hidden" type="hidden" name="verification_request[verification_request_comments_attributes][0][content]" />
                  </div>
                </div>
                <div class="video_capture pt-3">
                  <div class="d-flex flex-column align-items-center justify-content-center">
                    <button class="btn-open-camera" id="open" type="button">
                      <i class="fas fa-spinner fa-spin d-none" id="cameraSpinner"></i>
                      <i class="fas fa-video" id="cameraIcon"></i>
                      Open Camera
                    </button>
                    
                   
                  </div>
                  <div aria-labelledby="camVerifyModalLabel" class="modal fade" id="camVerifyModal" role="dialog" tabindex="-1">
                    <div class="modal-dialog" data-timer="3" role="document">
                      <div class="modal-content p-0">
                        <div class="modal-header">
                          <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <h4 class="modal-title text-center py-0 py-sm-3" id="camVerifyModalLabel">Hold a piece of paper with the following code: <code><b>{{ $photo_code }}</b></code>
                          </h4>
                        </div>
                        <div class="modal-body p-0 position-relative">
                          <button class="btn btn-circle p-1 position-absolute align-items-center z-index-1" data-btn-change="" style="top:1rem;left:1rem;display:flex" title="Flip camera" type="button">
                            <svg width="28" height="28" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38.35 28">
                              <path d="M14.83,25.6c-3.86-.37-7.28-1.22-9.83-2.4-3.12-1.43-5-3.35-5-5.5s2.02-4.21,5.34-5.65v2.01c-1.97,1.05-3.13,2.31-3.13,3.65,0,1.5,1.48,2.92,3.92,4.04,2.25,1.03,5.26,1.79,8.71,2.14v-2.26l.15.1,2.33,1.52,2.33,1.52.08.05-.05.03-.03.02-2.33,1.52-2.33,1.52-.15.1v-2.4h0ZM9.98,1.5h5.87V.22c0-.12.1-.22.22-.22h6.09c.12,0,.22.1.22.22v1.27h5.98c1.34,0,2.44,1.1,2.44,2.44v12.18c0,1.34-1.1,2.44-2.44,2.44H9.98c-1.34,0-2.44-1.1-2.44-2.44V3.93c0-1.34,1.1-2.44,2.44-2.44h0ZM19.17,7.72c1.22,0,2.21.99,2.21,2.22s-.99,2.21-2.21,2.21-2.21-.99-2.21-2.21.99-2.22,2.21-2.22h0ZM26.73,4.01c.48,0,.87.39.87.87s-.39.87-.87.87-.87-.39-.87-.87.39-.87.87-.87h0ZM19.17,5.51c2.49,0,4.51,2.02,4.51,4.51s-2.02,4.51-4.51,4.51-4.51-2.02-4.51-4.51,2.02-4.51,4.51-4.51h0ZM33.01,12.05c3.31,1.44,5.34,3.43,5.34,5.65s-1.89,4.07-5,5.5c-2.95,1.36-7.05,2.29-11.67,2.54l-.07-.85-.07-.85c4.27-.23,8.03-1.08,10.7-2.3,2.43-1.12,3.91-2.53,3.91-4.03,0-1.33-1.16-2.6-3.13-3.65v-2.01h0Z" fill="#000" fill-rule="evenodd" stroke-width="0" />
                            </svg>
                          </button>
                          <div class="capture-wrapper">
                            <video autoplay="" id="cap" playsInline="true"></video>
                            <div class="preview hidden position-absolute" id="preview_frame" style="top:0;left:0;right:0;bottom:0"></div>
                          </div>
                        </div>
                        <div class="d-flex justify-content-between p-2 p-sm-3" style="border-top: 1px solid #2a2a2a;">
                          <button class="btn btn-default hidden" data-reset-frame="" type="button">Try again</button>
                          <button class="align-items-center btn btn-success hidden" id="get_frm" style="display:inline-flex" type="button">
                            <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20">
                              <path fill="currentColor" d="M206.309-108.001q-41.033 0-69.67-28.638-28.638-28.637-28.638-69.67V-358H194v151.691q0 4.616 3.846 8.463 3.847 3.846 8.463 3.846H358v85.999H206.309Zm395.691 0V-194h151.691q4.616 0 8.463-3.846 3.846-3.847 3.846-8.463V-358h85.999v151.691q0 41.033-28.638 69.67-28.637 28.638-69.67 28.638H602ZM108.001-602v-151.691q0-41.033 28.638-69.67 28.637-28.638 69.67-28.638H358V-766H206.309q-4.616 0-8.463 3.846-3.846 3.847-3.846 8.463V-602h-85.999ZM766-602v-151.691q0-4.616-3.846-8.463-3.847-3.846-8.463-3.846H602v-85.999h151.691q41.033 0 69.67 28.638 28.638 28.637 28.638 69.67V-602H766ZM480-272.001q-86.542 0-147.271-60.728Q272.001-393.458 272.001-480q0-86.542 60.728-147.271Q393.458-687.999 480-687.999q86.542 0 147.271 60.728Q687.999-566.542 687.999-480q0 86.542-60.728 147.271Q566.542-272.001 480-272.001Zm.118-85.999Q531-358 566.5-393.618q35.5-35.617 35.5-86.5Q602-531 566.382-566.5q-35.617-35.5-86.5-35.5Q429-602 393.5-566.382q-35.5 35.617-35.5 86.5Q358-429 393.618-393.5q35.617 35.5 86.5 35.5ZM480-480Z" />
                            </svg>
                            <span>Take Photo</span>
                          </button>
                          <button class="btn btn-primary" data-btn-submit="" disabled="" type="submit">Submit for verification</button>
                        </div>
                        <p class="px-3 d-flex align-items-center">
                          <i class="fa fa-exclamation-triangle mr-2"></i>
                          <span>The image you upload will <b>never</b> be published or shared.</span>
                        </p>
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
          <div class="col-sm-6">
            <div class="verify-warning">
              We do <b>not</b> accept passport or ID scans or photos that are not clearly of the same person as on the listing.<br />
              <b>Any attempt to use different person's photos after verification will result in a verification ban.</b>
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
    </div>
  </div>

  @push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('open');
    const spinner = document.getElementById('cameraSpinner');
    const cameraIcon = document.getElementById('cameraIcon');

    if (openBtn) {
        openBtn.addEventListener('click', function() {
            if (spinner) spinner.classList.remove('d-none');
            if (cameraIcon) cameraIcon.classList.add('d-none');
        });
    }

    $('#camVerifyModal').on('shown.bs.modal', function() {
        if (spinner) spinner.classList.add('d-none');
        if (cameraIcon) cameraIcon.classList.remove('d-none');
    });
});
</script>
  @endpush