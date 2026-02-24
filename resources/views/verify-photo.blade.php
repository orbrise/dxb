@extends('layouts.verify')

@section('content')
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
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
.modal-body {
    padding: 0;
}
.video-container {
    position: relative;
    width: 100%;
    min-height: 300px;
    background: #000;
}
.profile-selector {
    background: transparent;
    border: none;
    padding: 0;
    margin-bottom: 20px;
    position: relative;
}
.profile-selector h4 {
    color: #dca623;
    font-weight: normal;
    font-size: 16px;
    white-space: nowrap;
}
.profile-search-wrapper {
    position: relative;
    flex: 1;
    max-width: 400px;
}
@media (max-width: 575px) {
    .profile-search-wrapper {
        max-width: 100%;
    }
}
.profile-search-wrapper .input-group {
    display: flex;
    flex-wrap: nowrap;
}
.profile-search-wrapper .input-group-append {
    display: flex;
    margin-left: -1px;
}
.profile-selector .d-flex {
    gap: 1px;
}
#profileResults {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: #2b2b2b;
    border: 1px solid #444;
    border-radius: 4px;
    margin-top: 5px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
#profileResults.show {
    display: block;
}
.profile-option {
    padding: 1px 8px;
    cursor: pointer;
    background: #2b2b2b;
    transition: all 0.2s;
    color: #fff;
    border-bottom: 1px solid #333;
}
.profile-option:last-child {
    border-bottom: none;
}
.profile-option:hover {
    background: #3a3a3a;
}
.profile-option.selected {
    background: #dca623;
    color: #000;
    font-weight: 500;
}
.profile-thumb {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 0px;
    margin-right: 10px;
}
#profileResults::-webkit-scrollbar {
    width: 8px;
}
#profileResults::-webkit-scrollbar-track {
    background: #1a1a1a;
}
#profileResults::-webkit-scrollbar-thumb {
    background: #555;
    border-radius: 4px;
}
#profileResults::-webkit-scrollbar-thumb:hover {
    background: #666;
}
#profileSearch {
    background: #2b2b2b;
    border: 1px solid #444;
    color: #fff;
}
#profileSearch:focus {
    background: #333;
    border-color: #dca623;
    color: #fff;
}
#searchBtn {
    background: #dca623;
    border-color: #dca623;
    color: #000;
}
#searchBtn:hover {
    background: #c49520;
    border-color: #c49520;
}
#searchBtn i {
    color: #000;
}
#profileSearch::placeholder {
    color: #999;
}
.text-muted {
    color: #aaa !important;
}
    </style>
@endpush

@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/my-profile/sana-khan-764ef4fa-698d-43e9-8ea9-90690fe782d2">
        <i class="fas fa-angle-left fa-fw"></i>
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
        
        <!-- Profile Selector -->
        <div class="profile-selector">
          <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center mb-3">
            <h4 class="mb-2 mb-sm-0 mr-sm-3"><i class="fas fa-user-circle mr-2"></i>Select Profile to Verify</h4>
            <div class="profile-search-wrapper w-100 w-sm-auto">
              <div class="input-group">
                <input type="text" 
                       id="profileSearch" 
                       class="form-control" 
                       placeholder="Type profile name or ID to search..."
                       autocomplete="off">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button" id="searchBtn">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
              
              <div id="profileResults"></div>
            </div>
          </div>
        </div>

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
                              <img alt="" class="img-responsive" data-original-height="960" data-original-width="768" src="{{smart_asset("userimages/".$user->user_id."/".$user->id."/".$user->singleimg->image)}}" width="115" />
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
                      </code>  &nbsp;on a piece of paper and take a new photo of yourself <i>holding that piece of paper</i>. </span>
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
                    <button type="button" class="btn btn-success px-5 fs-large" id="openVerificationCamera">
                        <i class="fas fa-spinnern fa-spinn mr-2" style="display:none !important;" id="cameraSpinner"></i>
                        <i class="fas fa-video mr-2" id="cameraIcon"></i>Open camera
                    </button>
                    {{-- <div class="d-none d-sm-flex flex-column align-items-center">
                      <p class="pt-3 px-3 text-center fs-larger">No camera on this device? <br>Scan the QR code with your mobile phone. </p>
                      <div class="qr-code-wrapper p-2">
                        
                      </div>
                    </div> --}}
                    {{-- <div class="mt-4 mb-0 alert alert-info py-2">
                      <i class="fas fa-info-circle mr-2"></i>
                      <span>If you manage this profile on behalf of a model you can share a verification link with them&nbsp;</span>
                      <span class="hidden" id="verifLink">https://mr.verifajo.io/v/ZJB/rpHWv7roLWyL5OiUdnGgcg==--ar1Krpi+3JdO37o7--4wI041mfwhhqxWKPp0ykiQ==</span>
                      <button class="px-0 btn-link d-inline-flex align-items-center" data-copy-btn="#verifLink" type="button">
                        <i class="fas fa-arrow-right mr-1"></i>
                        <span class="fw-bold" data-text="copied to clipboard">copy verification URL</span>
                      </button>
                    </div> --}}
                  </div>
                  <div class="modal fade" id="verificationCameraModal" role="dialog" tabindex="-1" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog" data-timer="3" role="document">
                        <div class="modal-content p-0">
                            <div class="modal-header">
                                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title text-center py-0 py-sm-3">
                                    Hold a piece of paper with the following code: 
                                    <code><b>{{$photo_code}}</b></code>
                                </h4>
                            </div>
                            <div class="modal-body p-0 position-relative">
                                <button class="btn btn-circle p-1 position-absolute align-items-center z-index-1" 
                                        data-btn-change="" 
                                        style="top:1rem;left:1rem;display:flex" 
                                        title="Flip camera" 
                                        type="button">
                                    <svg width="28" height="28" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38.35 28">
                                        <path d="M14.83,25.6c-3.86-.37-7.28-1.22-9.83-2.4-3.12-1.43-5-3.35-5-5.5s2.02-4.21,5.34-5.65v2.01c-1.97,1.05-3.13,2.31-3.13,3.65,0,1.5,1.48,2.92,3.92,4.04,2.25,1.03,5.26,1.79,8.71,2.14v-2.26l.15.1,2.33,1.52,2.33,1.52.08.05-.05.03-.03.02-2.33,1.52-2.33,1.52-.15.1v-2.4h0Z" fill="#000" fill-rule="evenodd"/>
                                    </svg>
                                </button>
                                <div class="capture-wrapper">
                                    <video autoplay="" id="verificationVideo" playsInline="true"></video>
                                    <div class="preview hidden position-absolute" id="verificationPreview" style="top:0;left:0;right:0;bottom:0"></div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between p-2 p-sm-3" style="border-top: 1px solid #474747;">
                                <button class="btn btn-default hidden" data-reset-frame="" type="button">Try again</button>
                                <button class="align-items-center btn btn-success" id="captureVerificationBtn" style="display:inline-flex" type="button">
                                  <svg class="mr-2" xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20"><path fill="currentColor" d="M206.309-108.001q-41.033 0-69.67-28.638-28.638-28.637-28.638-69.67V-358H194v151.691q0 4.616 3.846 8.463 3.847 3.846 8.463 3.846H358v85.999H206.309Zm395.691 0V-194h151.691q4.616 0 8.463-3.846 3.846-3.847 3.846-8.463V-358h85.999v151.691q0 41.033-28.638 69.67-28.637 28.638-69.67 28.638H602ZM108.001-602v-151.691q0-41.033 28.638-69.67 28.637-28.638 69.67-28.638H358V-766H206.309q-4.616 0-8.463 3.846-3.846 3.847-3.846 8.463V-602h-85.999ZM766-602v-151.691q0-4.616-3.846-8.463-3.847-3.846-8.463-3.846H602v-85.999h151.691q41.033 0 69.67 28.638 28.638 28.637 28.638 69.67V-602H766ZM480-272.001q-86.542 0-147.271-60.728Q272.001-393.458 272.001-480q0-86.542 60.728-147.271Q393.458-687.999 480-687.999q86.542 0 147.271 60.728Q687.999-566.542 687.999-480q0 86.542-60.728 147.271Q566.542-272.001 480-272.001Zm.118-85.999Q531-358 566.5-393.618q35.5-35.617 35.5-86.5Q602-531 566.382-566.5q-35.617-35.5-86.5-35.5Q429-602 393.5-566.382q-35.5 35.617-35.5 86.5Q358-429 393.618-393.5q35.617 35.5 86.5 35.5ZM480-480Z"></path></svg>
                                    <span>Take Photo</span>
                                </button>
                                <button class="btn btn-primary" id="submitVerificationBtn" disabled="" type="submit">Submit for verification</button>
                            </div>
                            <p class="px-3 d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
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

  </div>
@endsection
  @push('js')
<script>
    // Make function globally available for Livewire
    window.initVerificationPage = function() {
    // Reset spinner state on page init
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
    const spinner = document.getElementById('cameraSpinner');
    const cameraIcon = document.getElementById('cameraIcon');
    let stream = null;
    
    // Skip if elements don't exist (wrong page)
    if (!openBtn) return;
    
    // Remove old handlers and reset state for re-initialization
    const newOpenBtn = openBtn.cloneNode(true);
    openBtn.parentNode.replaceChild(newOpenBtn, openBtn);
    
    // Get fresh references after clone
    const freshOpenBtn = document.getElementById('openVerificationCamera');
    const freshSpinner = document.getElementById('cameraSpinner');
    const freshCameraIcon = document.getElementById('cameraIcon');
    
    // Ensure spinner is hidden
    if (freshSpinner) freshSpinner.style.display = 'none';
    if (freshCameraIcon) freshCameraIcon.style.display = 'inline-block';

    // Modal helper functions (vanilla JS)
    function showModal() {
        modalEl.style.display = 'block';
        modalEl.classList.add('show', 'in');
        document.body.classList.add('modal-open');
        
        // Create backdrop if it doesn't exist
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
        
        // Remove backdrop
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        
        // Stop camera stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            stream = null;
        }
        
        // Reset UI for next time
        video.style.display = '';
        captureBtn.style.display = '';
        preview.classList.add('hidden');
        preview.innerHTML = '';
        submitBtn.disabled = true;
        delete submitBtn.dataset.imageData;
    }

    freshOpenBtn.onclick = async () => {
        try {
            // Show spinner, hide camera icon
            freshSpinner.style.display = 'inline-block';
            freshCameraIcon.style.display = 'none';
            
            stream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: 1280, height: 720 } 
            });
            video.srcObject = stream;
            await video.play();
            
            // Hide spinner, show camera icon
            freshSpinner.style.display = 'none';
            freshCameraIcon.style.display = 'inline-block';
            
            showModal();
        } catch (err) {
            console.error('Camera error:', err);
            // Hide spinner, show camera icon on error
            freshSpinner.style.display = 'none';
            freshCameraIcon.style.display = 'inline-block';
            alert('Could not access camera. Please ensure camera permissions are granted.');
        }
    };
    
    // Close modal on X button click
    modalEl.querySelector('[data-dismiss="modal"]').onclick = function() {
        hideModal();
    };
    
    // Close modal on backdrop click
    modalEl.onclick = function(e) {
        if (e.target === modalEl) {
            hideModal();
        }
    };

    captureBtn.onclick = () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0);
        
        const imageData = canvas.toDataURL('image/jpeg', 1.0);
        
        // Show preview
        preview.innerHTML = `<img src="${imageData}" style="width:100%;height:100%;object-fit:contain">`;
        preview.classList.remove('hidden');
        
        // Update UI
        video.style.display = 'none';
        captureBtn.style.display = 'none';
        submitBtn.removeAttribute('disabled');
        
        // Store image data for submission
        submitBtn.dataset.imageData = imageData;
    };

    submitBtn.onclick = () => {
    const imageData = submitBtn.dataset.imageData;
    
    if (!imageData) {
        alert('No image captured!');
        return;
    }
    
    const formData = new FormData();
    formData.append('photoData', imageData);
    formData.append('_token', '{{ csrf_token() }}');
    
    const slug = '{{ $user->slug }}';
    const id = '{{ $user->id }}';
    
    // Disable button and show loading
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
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Upload failed');
        }
        return response.text();
    })
    .then(data => {
        console.log('Upload successful');
        hideModal();
        // Redirect with success message in URL
        window.location.href = `/my-profile/${slug}/${id}?verification_success=1`;
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to upload photo. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit Photo';
    });
};

    // Profile Search and Selection
    let selectedProfileId = {{ $user->id }};
    let selectedProfileSlug = '{{ $user->slug }}';
    const profileSearch = document.getElementById('profileSearch');
    const profileResults = document.getElementById('profileResults');
    const searchBtn = document.getElementById('searchBtn');
    const assetBaseUrl = 'https://assets.massagerepublic.com.co/';
    
    // Search profiles using fetch API
    function searchProfiles(query) {
        console.log('Searching for:', query);
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
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Search failed with status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Search results:', data);
            displayProfiles(data.profiles);
        })
        .catch(error => {
            console.error('Search error:', error);
            profileResults.classList.remove('show');
            profileResults.innerHTML = '<div class="p-3 text-muted">Error loading profiles</div>';
            profileResults.classList.add('show');
        });
    }
    
    // Display profile results
    function displayProfiles(profiles) {
        profileResults.innerHTML = '';
        
        if (!profiles || profiles.length === 0) {
            profileResults.innerHTML = '<div class="p-3 text-muted">No profiles found</div>';
            profileResults.classList.add('show');
            return;
        }
        
        profiles.forEach(function(profile) {
            const isSelected = profile.id == selectedProfileId ? 'selected' : '';
            const isCurrent = profile.id == {{ $user->id }} ? ' <span style="color: #dca623;">(Current)</span>' : '';
            
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
            div.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${imageUrl}" class="profile-thumb" alt="${profile.name}" onerror="this.src='/assets/images/default-avatar.png'">
                    <div>
                        <strong>${profile.name}</strong>${isCurrent}
                        <br>
                        <small class="text-white">ID: ${profile.id}</small>
                    </div>
                </div>
            `;
            
            // Add click handler for profile selection
            div.addEventListener('click', function() {
                const profileId = this.getAttribute('data-profile-id');
                const slug = this.getAttribute('data-slug');
                window.location.href = '/my-profile/' + slug + '/' + profileId + '/verify-photo';
            });
            
            profileResults.appendChild(div);
        });
        
        profileResults.classList.add('show');
    }
    
    // Search as user types (debounced)
    let searchTimeout;
    profileSearch.addEventListener('input', function() {
        const query = this.value.trim();
        console.log('Input event fired, query:', query);
        
        clearTimeout(searchTimeout);
        
        if (query.length === 0) {
            profileResults.classList.remove('show');
            profileResults.innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(function() {
            searchProfiles(query);
        }, 300);
    });
    
    // Also listen for keyup as backup
    profileSearch.addEventListener('keyup', function(e) {
        // Skip if it's Enter key (handled separately)
        if (e.keyCode === 13) return;
        
        const query = this.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length === 0) {
            profileResults.classList.remove('show');
            profileResults.innerHTML = '';
            return;
        }
        
        searchTimeout = setTimeout(function() {
            searchProfiles(query);
        }, 300);
    });
    
    // Handle search button click
    searchBtn.addEventListener('click', function() {
        const query = profileSearch.value.trim();
        if (query.length > 0) {
            searchProfiles(query);
        }
    });
    
    // Handle Enter key in search box
    profileSearch.addEventListener('keypress', function(e) {
        if (e.which === 13 || e.keyCode === 13) {
            e.preventDefault();
            const query = this.value.trim();
            if (query.length > 0) {
                searchProfiles(query);
            }
        }
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.profile-search-wrapper')) {
            profileResults.classList.remove('show');
        }
    });
}

// Initialize on DOMContentLoaded (hard refresh)
document.addEventListener('DOMContentLoaded', window.initVerificationPage);

// Initialize on Livewire navigation (soft navigation)
document.addEventListener('livewire:navigated', window.initVerificationPage);

// Also try livewire:init for older versions
document.addEventListener('livewire:init', window.initVerificationPage);

// Run immediately if DOM is already loaded (in case script runs late)
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    window.initVerificationPage();
}
</script>
@endpush

{{-- Inline script that always runs on page load, even with Livewire navigation --}}
<script>
    // This runs every time the page content is loaded
    (function() {
        function tryInit() {
            if (typeof window.initVerificationPage === 'function') {
                window.initVerificationPage();
            } else {
                // Function not ready yet, try again shortly
                setTimeout(tryInit, 50);
            }
        }
        
        // Small delay to ensure DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', tryInit);
        } else {
            setTimeout(tryInit, 10);
        }
    })();
</script>
