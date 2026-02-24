@push('css')
<style>
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
    </style>
@endpush

@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/my-profile/sana-khan-764ef4fa-698d-43e9-8ea9-90690fe782d2">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">My Profile</span>
      </a>
      <div class="title">
        <h1>
          <a href="">Verification Application for </a>
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
                <ol class="step-list">
                  <li>
                    <span class="step-copy">The photos on the listing must be yours: <div class="image-gallery pb-thumbnails">
                        <a class=" pb-photo-link" href="https://d18fr84zq3fgpm.cloudfront.net/sana-khan-pakistani-escort-in-dubai-9275036_original.jpg">
                          <span class="img-wrapper featured">
                            <div class="image-wrapper">
                                @if(!empty($user->singleimg->image))
                              <img alt="Sana Khan - escort in Dubai Photo 1 of 1" class="img-responsive" data-original-height="960" data-original-width="768" src="{{smart_asset("userimages/".$user->user_id."/".$user->singleimg->image)}}" width="115" />
                            @endif
                            </div>
                          </span>
                        </a>
                      </div>
                    </span>
                  </li>
                  <li>
                    <span class="step-copy">We must be able to match them with the new photo you upload.</span>
                  </li>
                  <li>
                    <span class="step-copy">Write <code>
                        <b>{{$photo_code}}</b>
                      </code> on a piece of paper and take a new photo of yourself <i>holding that piece of paper</i>. </span>
                  </li>
                  <li>
                    <span class="step-copy">Face not shown in profile photos? Take a verification photo showing the same outfit and visible body features.</span>
                  </li>
                </ol>
              </div>
              <form class="simple_form validate" id="new_verification_request" novalidate="novalidate" action="/my-profile/{{$user->slug}}/{{$user->id}}/verification_request" accept-charset="UTF-8" method="post">
                @csrf
                <input type="hidden" id="photo_code" name="photo_code" value="{{$photo_code}}" />
                <input name="utf8" type="hidden" value="&#x2713;" />
                <input type="hidden" name="authenticity_token" value="+SxeiNrzmjFs8AnKCIotVf/ajvMC6e342poc9ryNJzZw5b0zWsDzGsat9WCOYuDb5Izpr/F55F379zcatlPM9g==" />
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
                    <button class="btn btn-success px-5 fs-large" id="open" type="button">
                      <i class="fas fa-spinner fa-spin mr-2 d-none" id="cameraSpinner"></i>
                      <i class="fas fa-video mr-2" id="cameraIcon"></i>Open camera </button>
                    <div class="d-none d-sm-flex flex-column align-items-center">
                      <p class="pt-3 px-3 text-center fs-larger">No camera on this device? <br>Scan the QR code with your mobile phone. </p>
                      <div class="qr-code-wrapper p-2">
                        
                      </div>
                    </div>
                    <div class="mt-4 mb-0 alert alert-info py-2">
                      <i class="fa fa-info-circle mr-2"></i>
                      <span>If you manage this profile on behalf of a model you can share a verification link with them&nbsp;</span>
                      <span class="hidden" id="verifLink">https://mr.verifajo.io/v/ZJB/rpHWv7roLWyL5OiUdnGgcg==--ar1Krpi+3JdO37o7--4wI041mfwhhqxWKPp0ykiQ==</span>
                      <button class="px-0 btn-link d-inline-flex align-items-center" data-copy-btn="#verifLink" type="button">
                        <i class="fa fa-arrow-right mr-1"></i>
                        <span class="fw-bold" data-text="copied to clipboard">copy verification URL</span>
                      </button>
                    </div>
                  </div>
                  <div aria-labelledby="camVerifyModalLabel" class="modal fade" id="camVerifyModal" role="dialog" tabindex="-1">
                    <div class="modal-dialog" data-timer="3" role="document">
                      <div class="modal-content p-0">
                        <div class="modal-header">
                          <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true">&times;</span>
                          </button>
                          <h4 class="modal-title text-center py-0 py-sm-3" id="camVerifyModalLabel">Hold a piece of paper with the following code: <code>
                              <b>{{$photo_code}}</b>
                            </code>
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
                        <div class="d-flex justify-content-between p-2 p-sm-3" style="border-top: 1px solid #474747;">
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
                          <span>The image you upload will <b>never</b> be published or shared. </span>
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
            <p class="alert alert-warning">We do <b>not</b> accept passport or ID scans or photos that are not clearly of the same person as on the listing. <br />
              <b>Any attempt to use different person's photos after verification will result in a verification ban.</b>
            </p>
            <div class="pitch" id="verify-pitch">
              <h2>Why verify your photos?</h2>
              <ul class="list-unstyled">
                <li>
                  <h3>Priority</h3>
                  <p>Listings with verified photos are shown ahead of non-verified ones</p>
                </li>
                <li>
                  <h3>Trust</h3>
                  <p>Users value real photos, and thus are more likely to contact you</p>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Camera Modal -->

  </div>

  @push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const openBtn = document.getElementById('open');
    const spinner = document.getElementById('cameraSpinner');
    const cameraIcon = document.getElementById('cameraIcon');
    
    if (openBtn) {
        openBtn.addEventListener('click', function() {
            // Show spinner, hide camera icon
            if (spinner) spinner.classList.remove('d-none');
            if (cameraIcon) cameraIcon.classList.add('d-none');
        });
    }
    
    // Hide spinner when modal is shown
    $('#camVerifyModal').on('shown.bs.modal', function() {
        if (spinner) spinner.classList.add('d-none');
        if (cameraIcon) cameraIcon.classList.remove('d-none');
    });
});
</script>
  @endpush