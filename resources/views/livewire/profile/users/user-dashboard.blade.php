<div>
@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/female-escorts-in-dubai">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">Escorts in Dubai</span>
      </a>
      <div class="title">
        <h1>
          <a href="/my-profile/umer-329cee24-363b-4b1c-bf8f-a846d3b78c2e">{{Auth::user()->name}}'s Dubai profile</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

<!-- Success Message Alert -->
<div id="success-alert" class="alert alert-success alert-dismissible" style="display: none; position: fixed; top: 80px; right: 20px; z-index: 99999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
    <button type="button" class="close" onclick="this.parentElement.style.display='none'">&times;</button>
    <strong><i class="fa fa-check-circle"></i> Success!</strong>
    <span id="success-message-text"></span>
</div>

<style>


/* Simple Clean Pagination - Theme Colors */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 30px 0;
    gap: 5px;
}

.pagination li {
    list-style: none;
}

.pagination li a,
.pagination li span {
    display: block;
    padding: 10px 16px;
    min-width: 45px;
    text-align: center;
    font-size: 16px;
    font-weight: 600;
    color: #555;
    background-color: #fff;
    border: 2px solid #ddd;
    text-decoration: none;
    transition: all 0.2s;
    border-radius: 4px;
}

.pagination li a:hover {
    background-color: #333;
    color: #dca623;
    border-color: #333;
}

.pagination li.active span {
    background-color: #333;
    color: #dca623;
    border-color: #333;
}

.pagination li.disabled span {
    color: #646464;
    cursor: not-allowed;
    background-color: #8f8f8f;
    border-color: #8f8f8f;
}

/* Previous/Next buttons */
.pagination li:first-child a:hover,
.pagination li:last-child a:hover {
    background-color: #333;
    color: #dca623;
}

@media (max-width: 576px) {
    .pagination li a,
    .pagination li span {
        padding: 8px 12px;
        min-width: 40px;
        font-size: 14px;
    }
}



.pagination>li>a, .pagination>li>button, .pagination>li>span {
    font-size: 18px;
    height: 48px;
    line-height: 26px;
    padding: 11px 16px;
    background: #535353;
    border: 1px solid #535353;
}

.pagination>li:last-child>a, .pagination>li:last-child>button {
    border-radius: 4px;
    padding: 0px 19px;
}

/* Profile Views Badge */
.profile-views-badge {
    display: inline-block;
    font-size: 14px;
    font-weight: normal;
    color: #dca623;
    padding: 4px 10px;
    background-color: rgba(220, 166, 35, 0.1);
    border-radius: 4px;
    border: 1px solid rgba(220, 166, 35, 0.3);
    white-space: nowrap;
}

.profile-views-badge i {
    margin-right: 4px;
    color: #dca623;
}

@media (max-width: 576px) {
    .profile-views-badge {
        font-size: 12px;
        padding: 3px 8px;
    }
}

/* Custom Tooltip Styles */
.help-tooltip {
    position: relative;
    cursor: help;
}

.help-tooltip:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 10px;
    padding: 10px 15px;
    background-color: #333;
    color: #fff;
    font-size: 13px;
    line-height: 1.4;
    border-radius: 4px;
    white-space: normal;
    width: 280px;
    max-width: 280px;
    text-align: center;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}

.help-tooltip:hover::before {
    content: '';
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    margin-bottom: 2px;
    border: 8px solid transparent;
    border-top-color: #333;
    z-index: 1001;
}

@media (max-width: 768px) {
    .help-tooltip {
        display: none !important;
    }
}

.margin-right {
    margin-right: 8px !important;
}

/* Modal backdrop transparent */
.modal-backdrop {
    background-color: rgba(0, 0, 0, 0.3) !important;
}

.modal-backdrop.show {
    opacity: 1 !important;
}

.nbtn {
  background: transparent;
    color: #aaaaaa;
    border-radius: 30px;
    border: 1px solid #4e4e4e;
    padding: 2px 10px;
}

.nbtn:hover {
    color:white;
}

/* Package Info Box Styles - Yellow/Golden Theme */
.package-info-box {
    background: rgba(0, 0, 0, 0.85);
    border: 2px solid #d4a017;
    border-radius: 10px;
    padding: 16px 20px;
    margin-top: 12px;
    box-shadow: 0 0 10px rgba(212, 160, 23, 0.3);
    max-width: 300px;
}

.package-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
}

.package-header i {
    color: #d4a017;
    font-size: 16px;
}

.package-header strong {
    color: #d4a017;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.package-expiry {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
    color: #e0e0e0;
    line-height: 32px;
    clear: both;
}

.package-expiry i {
    color: #d4a017;
    font-size: 15px;
}

.days-remaining {
    color: #ffffff;
    font-weight: 500;
}

/* Ensure thumbs container contains all child elements */
.thumbs {
    display: flex;
    flex-direction: column;
    float: left;
    margin-right: 15px;
}

/* Hide mobile-only about text on desktop */
.mobile-only-about {
    display: none;
}

/* Mobile Optimization for Filter Buttons */
@media (max-width: 768px) {
    .btn-group {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 100%;
        gap: 8px;
    }
    
    .btn-group .btn {
        text-align: center;
        padding: 12px 8px !important;
        border-radius: 6px !important;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .btn-group .btn i {
        margin-right: 6px;
        flex-shrink: 0;
    }
    
    .btn-group .btn-primary {
        background: linear-gradient(135deg, #f4b827 0%, #d3980b 100%);
        border-color: #c9910a;
        color: #333;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(244, 184, 39, 0.3);
    }
    
    .btn-group .nbtn {
        background: #2b2b2b;
        border: 1px solid #444;
        color: #aaa;
    }
    
    .btn-group .nbtn:active {
        background: #333;
        color: #fff;
    }
    
    .mb-4 {
        margin-bottom: 1.5rem !important;
    }
    
    .my-listing-new-link {
        width: 100%;
        margin-top: 12px;
        padding: 12px 16px !important;
        font-size: 16px;
        font-weight: 600;
        border-radius: 6px !important;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .listing-li .img-wrapper.basic img {
        width: 350px;
        height: 300px;
        object-fit: contain;
    }

    .nostyle-link, .nostyle-link:hover {
        color: #fff;
        text-decoration: none;
        display: flex;
    }
    
    .visible-xxs .profile-views-badge {
        display: inline-block !important;
        margin-top: 0 !important;
        margin-right: 3px !important;
    }
    
    .visible-xxs {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-start;
    }
    
    .visible-xxs .nostyle-link {
        width: 100%;
        margin-bottom: 10px;
        margin-left: 11px;
    }
    
    .package-expiry {
        display: none !important;
    }
    
    /* Fix pagination visibility on mobile */
    .pagination {
        display: flex !important;
        flex-wrap: wrap;
        overflow-x: auto;
    }
    
    .pagination li {
        display: inline-block !important;
    }
    
    .pagination li a,
    .pagination li span {
        display: block !important;
        visibility: visible !important;
    }
    
        .my-profile .my-listing-actions {
        margin-top: 5px;
    }

    .my-profile {
      margin-top: 30px;
    }

    .my-profile-primary-block {
    position: relative;
    margin-top: 0rem;
    padding-top: 0px;
}

    .my-profile .my-listing-actions {
        margin-bottom: 8px;
    }

    .mobile-only-about {
        display: block !important;
    }
    

    .listing-info-wrapper {
          border-bottom: 1px solid #717171;
    }
}

/* Tablet optimization */


/* Tablet optimization */
@media (min-width: 769px) and (max-width: 992px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .btn-group .btn {
        flex: 1 1 calc(50% - 4px);
        min-width: calc(50% - 4px);
    }

    

}

.days-remaining.text-danger {
    color: #ff4444 !important;
}



/* Profile Filter Buttons */
.btn-group .btn {
    margin-right: 5px;
    border-radius: 4px !important;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.btn-group .btn i {
    margin-right: 5px;
}

@media (min-width: 568px) {
    .listing-li .listing-info {
        height: 138px;
    }
}
@media (min-width: 768px) {
    .activity-record.mini .img-wrapper.basic img, .listing-li .img-wrapper.basic img, .listings-grid .img-wrapper.basic img {
        width: 89px;
        height: 95px;
        object-fit: cover;
    }

   
}

@media (max-width: 768px) {
     .nbtn {
    padding: 2px 7px !important;
}

.my-profile .listing-li {
  border-bottom:0px !important;
}

.my-profile .listing-li {
    padding-bottom: 10px !important;
}

.margin-right {
    margin-right: 5px !important;
}

}


.btn-primary.active, .btn-primary:active, .btn-primary:hover, .my-listings-nav.open>.dropdown-toggle.my-listing-new-link, .my-listings-nav>.active.my-listing-new-link, .my-listings-nav>.my-listing-new-link:active, .my-listings-nav>.my-listing-new-link:hover, .open>.btn-primary.dropdown-toggle {
    color: #333 !important;
    background-color: transparent;
    border-color: #747474ff;
}
</style>

<div class="container-fluid">

<div class="content-wrapper no-sidebar">
        <div id="content">
          <div class="col-12">
            @if(session('success'))
            <div class="alert alert-success" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
                <i class="fa fa-check-circle"></i>
                <span class="sr-only">Success:</span>
                {{ session('success') }}
                  </div>
                  @endif
                 

          </div>
@include('components.profile-dashboard-nav')
          {{-- <div class="mb-4">
                    <div class="btn-group" role="group">
                      <a style="    padding: 6px 12px;" href="{{ route('user.dashboard', ['name' => $user->slug, 'id' => $user->id]) }}" 
                         class="btn {{ !request()->has('filter') ? 'btn-primary' : 'nbtn' }}">
                        <i class="fa fa-check-circle"></i> Active Profiles ({{ $activeCount ?? 0 }})
                      </a>
                      <a style="    padding: 6px 12px;" href="{{ route('rejected.verifications') }}" 
                         class="btn {{ request()->routeIs('rejected.verifications') ? 'btn-primary' : 'nbtn' }}">
                        <i class="fa fa-exclamation-triangle"></i> Rejected ({{ $rejectedCount ?? 0 }})
                      </a>
                      <a style="    padding: 6px 12px;" href="{{ route('user.dashboard', ['name' => $user->slug, 'id' => $user->id, 'filter' => 'pending']) }}" 
                         class="btn {{ request()->get('filter') == 'pending' ? 'btn-primary' : 'nbtn' }}">
                        <i class="fa fa-clock"></i> Pending Approval ({{ $pendingCount ?? 0 }})
                      </a>
                      <a style="    padding: 6px 12px;" href="{{ route('profile.archived') }}" 
                         class="btn {{ request()->routeIs('profile.archived') ? 'btn-primary' : 'nbtn' }}">
                        <i class="fa fa-clock"></i> Archived ({{ $archivedCount ?? 0 }})
                      </a>

                     
          
                    </div>
                     <a style="color: #333; float:right; background: #f4b827 linear-gradient(#f4b827, #d3980b) repeat-x;
    border-color: #c9910a;" class="btn my-listing-new-link " href="{{ route('new.profile') }}" data-turbolinks="false">
              Add Profile <i class="fa fa-plus fa-text-default"></i>
          </a>


                  </div> --}}
         



          
          <div class="block my-profile-primary-block">
            <div class="row">
              <div class="col-md-8">
                <div class="my-profile">
                  
                  
                  <!-- Profile Filter Links -->
                  
                  
                  {{-- Check if user has any rejected verifications --}}
                  @php
                    $hasRejections = false;
                    foreach($allProfiles as $p) {
                        if($p->rejectedVerification) {
                            $hasRejections = true;
                            break;
                        }
                    }
                  @endphp
                  
                  @if($hasRejections)
                  <div class="alert alert-danger alert-dismissible" role="alert">
                    <button class="close" data-dismiss="alert" type="button">
                      <i class="fa fa-times"></i>
                      <span class="sr-only">Close</span>
                    </button>
                    <h4><i class="fa fa-exclamation-triangle"></i> Photo Verification Rejected</h4>
                    <p>One or more of your photo verifications have been rejected. Please review the reasons and re-submit.</p>
                    <a href="{{ route('rejected.verifications') }}" class="btn btn-danger btn-sm">
                      <i class="fa fa-eye"></i> View Rejected Verifications
                    </a>
                  </div>
                  @endif
                  
                  @if(isset($allProfiles) && $allProfiles->total() > 0)
                    @foreach($allProfiles as $profile)
                    @if($profile->package_id && $profile->getpackage)
                    @php
                              // Get package tiers
                              $priceTiers = $profile->getpackage->price_tiers ? json_decode($profile->getpackage->price_tiers, true) : null;
                              $expiresAt = null;
                              $daysRemaining = null;
                              
                              // Check if promoted_until is set
                              if ($profile->promoted_until) {
                                  $expiresAt = \Carbon\Carbon::parse($profile->promoted_until);
                                  $now = \Carbon\Carbon::now();
                                  $daysRemaining = $now->diffInDays($expiresAt, false);
                                  $daysRemaining = max(0, ceil($daysRemaining));
                              } 
                              // Otherwise use the longest tier (30 days) from updated_at
                              elseif ($priceTiers && count($priceTiers) > 0) {
                                  // Get the last tier which has the most days
                                  $longestTier = end($priceTiers);
                                  $days = (int)$longestTier['days'];
                                  
                                  $packageStartDate = $profile->updated_at ?? $profile->created_at;
                                  $expiresAt = \Carbon\Carbon::parse($packageStartDate)->addDays($days);
                                  $now = \Carbon\Carbon::now();
                                  $daysRemaining = $now->diffInDays($expiresAt, false);
                                  $daysRemaining = max(0, ceil($daysRemaining));
                              }
                            @endphp
                          @endif


                    {{-- Normal Profile Layout --}}
                    <div class="listing-li free thumbs-0 thumbs-free mb-3 {{$profile->id}}">
                      <h2 class="visible-xxs">
                        <a class="nostyle-link" href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}" wire:navigate title="{{$profile->name}}, escort in {{$profile->getcity->name}}">
                          {{$profile->name}}
                          
                        </a>
                        <span class="profile-views-badge ml-2" title="Profile Views">
                          <i class="fa fa-eye"></i> {{ $profile->profile_views ?? 0 }}
                        </span>
                        <span class="profile-views-badge ml-2" title="Phone Clicks" style="border-color: rgba(40, 167, 69, 0.3); background-color: rgba(40, 167, 69, 0.1);">
                          <i class="fa fa-phone" style="color: #28a745;"></i> <span style="color: #28a745;">{{ $profile->phone_clicks ?? 0 }}</span>
                        </span>
                        @if($profile->package_id && $profile->getpackage)
                        <span class="profile-views-badge ml-2" style="border-color: rgba(220, 166, 35, 0.3); background-color: rgba(220, 166, 35, 0.1);">
                          <i class="fa fa-star" style="color: #dca623;"></i> <span style="color: #dca623;">{{ strtoupper($profile->getpackage->name ?? 'PACKAGE') }}</span>
                        </span>
                        @if($daysRemaining !== null && $daysRemaining > 0)
                        <span class="profile-views-badge ml-2" style="border-color: rgba(220, 166, 35, 0.3); background-color: rgba(220, 166, 35, 0.1);">
                          <i class="fa fa-clock" style="color: #dca623;"></i> <span style="color: #dca623;">{{ $daysRemaining }} {{ $daysRemaining == 1 ? 'day' : 'days' }} left</span>
                        </span>
                        @endif
                        @endif
                        @if($profile->activeAuction)
                        <span class="profile-views-badge ml-2" style="border-color: rgba(255, 193, 7, 0.5); background-color: rgba(255, 193, 7, 0.15);">
                          <i class="fa fa-gavel" style="color: #ffc107;"></i> <span style="color: #ffc107;">AUCTION SPOT #{{ $profile->activeAuction->spot_number }}</span>
                        </span>
                        <span class="profile-views-badge ml-2" style="border-color: rgba(40, 167, 69, 0.3); background-color: rgba(40, 167, 69, 0.1);">
                          <i class="fa fa-clock" style="color: #28a745;"></i> <span style="color: #28a745;">{{ $profile->auction_days_remaining }} {{ $profile->auction_days_remaining == 1 ? 'day' : 'days' }} left</span>
                        </span>
                        @endif
                      </h2>
                      <div class="thumbs">
                        <div class="main-thumbs">
                          <a class="img pb-photo-link" href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}" wire:navigate>
                            <span class="img-wrapper basic">
                              <div class="image-wrapper" style="position: relative; overflow: hidden;">
                                @if(!empty($profile->coverimg->image))
                                <!-- Blurred background -->
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}'); background-size: cover; background-position: center; filter: blur(10px); transform: scale(1.1);"></div>
                                <!-- Actual image on top -->
                                <img alt="{{$profile->name}}" class="img-responsive" style="position: relative; z-index: 1; object-fit: contain;" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" />
                                @elseif(!empty($profile->singleimg->image))
                                <!-- Blurred background -->
                                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}'); background-size: cover; background-position: center; filter: blur(10px); transform: scale(1.1);"></div>
                                <!-- Actual image on top -->
                                <img alt="{{$profile->name}}" class="img-responsive" style="position: relative; z-index: 1; object-fit: contain;" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" />
                                @else
                                <img alt="{{$profile->name}}" class="img-responsive" height="95" src="{{smart_asset('assets/images/default-avatar.png')}}" width="89" />
                                @endif 
                              </div>
                            </span>
                             
                          </a>

                         
                        </div>
                        @if($profile->package_id && $profile->getpackage && $daysRemaining !== null)
                        <div style="margin-top: 5px; font-size: 12px; color: {{ $daysRemaining <= 3 ? '#ff4444' : '#dca623' }};">
                          <i class="fa fa-clock"></i> {{ $daysRemaining > 0 ? $daysRemaining . ' ' . ($daysRemaining == 1 ? 'day' : 'days') . ' left' : 'Expired' }}
                        </div>
                        @endif
                      </div>
                      <div class="listing-info-wrapper" style="height:160px; ">
                        <div class="listing-info">
                          <h2>
                            <a class="nostyle-link" href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}" wire:navigate title="{{$profile->name}}, escort in {{$profile->getcity->name}}">
                              {{$profile->name}}
                              
                            </a>
                            <span class="profile-views-badge ml-2" title="Profile Views">
                              <i class="fa fa-eye"></i> {{ $profile->profile_views ?? 0 }}
                            </span>
                            <span class="profile-views-badge ml-2" title="Phone Clicks" style="border-color: rgba(40, 167, 69, 0.3); background-color: rgba(40, 167, 69, 0.1);">
                              <i class="fa fa-phone" style="color: #28a745;"></i> <span style="color: #28a745;">{{ $profile->phone_clicks ?? 0 }}</span>
                            </span>
                            @if($profile->package_id && $profile->getpackage)
                          <span class="profile-views-badge ml-2" style="font-size:13px">
                              
                              {{ strtoupper($profile->getpackage->name ?? 'PACKAGE') }}
                            
                            </span>
                            @endif
                            @if($profile->activeAuction)
                          <span class="profile-views-badge ml-2" style="font-size:13px; border-color: rgba(255, 193, 7, 0.5); background-color: rgba(255, 193, 7, 0.15);">
                              <i class="fa fa-gavel" style="color: #ffc107;"></i> <span style="color: #ffc107;">AUCTION #{{ $profile->activeAuction->spot_number }} - {{ $profile->auction_days_remaining }} {{ $profile->auction_days_remaining == 1 ? 'day' : 'days' }}</span>
                            </span>
                            @endif
                          </h2>
                           
                          <a class="nostyle-link" href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}" wire:navigate>
                            <p class="text-muted">
                              <i class="fa fa-map-marker-alt fa-inline"></i> {{$profile->getcity->name}}
                              <span class="mx-2">|</span>
                              <i class="fa fa-venus fa-inline"></i> {{$profile->ggender->name}}
                            </p>

                           
                            @if(!empty($profile->about))
                            <p>{{\Str::limit($profile->about, $limit=130, $end='...')}}</p>
                            @endif
                          </a>
                          
                         
                        </div>
                        <div class="my-listing-actions">
                          <a class="btn nbtn margin-right" href="{{url('edit-profile/'.$profile->slug.'/'.$profile->id)}}" wire:navigate>
                            <i class="fa fa-pencil-alt fa-inline"></i>Edit
                          </a>
                          <a class="btn nbtn margin-right" href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                            <i class="fa fa-eye fa-inline"></i>View
                          </a>
                          <a  class="btn nbtn margin-right" href="/my-profile/{{$profile->slug}}/{{$profile->id}}/upgrade" wire:navigate>
                            <i class="fa fa-arrow-up fa-inline"></i>Upgrade
                          </a>
                          @if($profile->is_active == 1)
                            <button type="button" class="btn nbtn profile-status-btn" 
                                    data-profile-id="{{ $profile->id }}" 
                                    data-action="0">
                              <i class="fa fa-pause fa-inline"></i>Pause
                            </button>
                          @else
                            <button type="button" class="btn nbtn profile-status-btn" 
                                    data-profile-id="{{ $profile->id }}" 
                                    data-action="1">
                              <i class="fa fa-play fa-inline"></i>Resume
                            </button>

                          @endif
                          <div class="help-tooltip d-inline-block" 
                               data-tooltip="Pausing advertising will mean your listing does not appear in search. If you have a paid listing, the remaining days will stay on your account. Your listing will remain active on the site for users who have bookmarked you.">
                            <i class="fa fa-info-circle fa-2x fa-fw text-muted"></i>
                          </div>

                         
                        </div>
                            @if(!empty($profile->about))
                            <p class="mobile-only-about" style="text-align: left;margin-left: 11px;">{{\Str::limit($profile->about, $limit=130, $end='...')}}</p>
                            @endif
                      </div>
                    </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    @if($allProfiles->hasPages())
                    <div class="mt-4">
                      {{ $allProfiles->links() }}
                    </div>
                    @endif
                  @endif
                  
                </div>
              </div>

              <div class="col-md-4">
                <div class="row">
              <div class="col-md-12">
                <div class="block block--goto-spots">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <h3 class="mt-0 border-bottom mb-2 d-flex align-items-center">
                        <img alt="Rocket launch icon" class="mr-2" height="28" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/rocket-2ad5d1c251dc15cf5ed832f505451edd4ae7ddef39b0c44a877a97baa34a84a8.svg" width="28" />
                        <span>Get to the top!</span>
                      </h3>
                      <p>Have you tried placing your profile at the top? </p>
                    </div>
                    
                  </div>
                  <a class="btn btn-black btn-lg d-inline-flex align-items-center" href="/auctions/female-escorts-in-dubai">
                        <span class="pr-2">See auctions</span>
                        <i class="fa fa-arrow-right"></i>
                      </a>
                </div>
                </div>
                <div class="col-md-12">
                <div class="own-listing-actions block">
                  <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                      <h3 class="no-margin-top border-bottom" >Free Listing</h3>
                      <p class="emphasized">Upgrade to VIP and get more enquiries!</p>
                    </div>
                  </div>
                  {{-- <a class="btn btn-lg btn-primary mt-3" wire:navigate href="/my-profile/{{$user->name}}/{{$user->id}}/upgrade">Upgrade now <i class="fa fa-arrow-right"></i>
                      </a> --}}
                </div>
             </div>
          </div>
              </div>
              
            </div>

              
          <div class="alert alert-warning alert-dismissible my-listing-suggestion-alert" role="alert">
            <button class="close" data-dismiss="alert" type="button">
              <i class="fa fa-times"></i>
              <span class="sr-only">Close</span>
            </button>
            <div class="my-listing-suggestion">
              <h2 class="my-listing-suggestion-title">More photos = More leads</h2>
              <p>Increasing the number of photos on your listing will produce more enquires - you can add up to 30 - <a href="https://massagerepublic.com/action/listings/umer-329cee24-363b-4b1c-bf8f-a846d3b78c2e/edit#photos">Add more photos</a>. </p>
            </div>
          </div>

            <div class="delete-listing">
              <a class="small text-muted" data-confirm-delete="Are you sure you want to DELETE this profile? This cannot be reversed.

" data-delete-item="profile" data-turbolinks="false" href="/action/listings/1486672" rel="nofollow">
                <i class="fa fa-times fa-inline"></i>Delete profile </a>
            </div>
          </div>
          <div class="row profile-secondary-blocks">
            <div class="col-sm-4">
              <div class="block" id="contacts-block">
                <h3 class="no-margin-top border-bottom padding-bottom">Contacts <small>(last 30 days) </small>
                </h3>
                <p>
                  <span class="my-stat margin-right" title="Total profile views across all your profiles">
                    <i class="fa fa-eye fa-inline"></i>{{ $totalViews ?? 0 }} </span>
                  <span class="my-stat margin-right" title="Total phone clicks across all your profiles">
                    <i class="fa fa-phone fa-inline"></i>{{ $totalPhoneClicks ?? 0 }} </span>
                </p>
                <p>
                  <a class="btn btn-primary" href="{{ route('user.statistics') }}">View statistics</a>
                </p>
              </div>
            </div>

          
            <div class="col-sm-8">
              <div class="row">
                <div class="col-sm-6">
                  <div class="block" id="verify-block">
                    <div class="photo pull-right text-center">
                      <a class=" pb-photo-link" href="/my-profile/umer-329cee24-363b-4b1c-bf8f-a846d3b78c2e/verification_request">
                        <span class="img-wrapper basic">
                          <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                            <i class="fa fa-check"></i>
                            <span>Verified photos</span>
                          </span>
                          <div class="image-wrapper">
                            @if(!empty($user->coverimg->image))
                            <img alt="{{$user->name}} - escort in Dubai Photo 1 of 1" class="img-responsive" height="95" src="{{smart_asset("/userimages/".$user->user_id."/".$user->id.'/'.$user->coverimg->image)}}" width="89" />
                            @else
                            <img alt="{{$user->name}} - escort in Dubai Photo 1 of 1" class="img-responsive" height="95" src="{{smart_asset("userimages/".$user->user_id."/".$user->id.'/'.$user->singleimg->image)}}" width="89" />
                            @endif   
                          </div>
                        </span>
                      </a>
                      <br />
                      <span class="text-muted">Preview</span>
                    </div>
                    <h3 class="no-margin-top border-bottom padding-bottom">Verify photos</h3>
                    <p>Verified profiles get more enquiries.</p>
<a class="btn btn-primary" href="{{ url('my-profile/'.$user->slug.'/'.$user->id.'/verify-photo')}}" wire:navigate>Verify now</a>


                    
                    <button class="px-0 btn-link d-flex align-items-center mt-3" data-copy-btn="#verifLink">
                      <svg class="mr-1" xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 -960 960 960" width="20">
                        <path fill="currentColor" d="M696-96q-50 0-85-35t-35-85q0-8 1-14.5t3-14.5L342-390q-15 16-35.354 23T264-360q-50 0-85-35t-35-85q0-50 35-85t85-35q22 0 42.5 7.5T342-570l238-145q-2-8-3-14.5t-1-14.5q0-50 35-85t85-35q50 0 85 35t35 85q0 50-35 85t-85 35q-22.292 0-42.646-7T618-654L380-509q2 8 3 14.5t1 14.5q0 8-1 14.5t-3 14.5l238 145q15-17 35.354-23.5T696-336q50 0 85 35t35 85q0 50-35 85t-85 35Zm0-600q20.4 0 34.2-13.8Q744-723.6 744-744q0-20.4-13.8-34.2Q716.4-792 696-792q-20.4 0-34.2 13.8Q648-764.4 648-744q0 20.4 13.8 34.2Q675.6-696 696-696ZM264-432q20.4 0 34.2-13.8Q312-459.6 312-480q0-20.4-13.8-34.2Q284.4-528 264-528q-20.4 0-34.2 13.8Q216-500.4 216-480q0 20.4 13.8 34.2Q243.6-432 264-432Zm432 264q20.4 0 34.2-13.8Q744-195.6 744-216q0-20.4-13.8-34.2Q716.4-264 696-264q-20.4 0-34.2 13.8Q648-236.4 648-216q0 20.4 13.8 34.2Q675.6-168 696-168Zm0-576ZM264-480Zm432 264Z" />
                      </svg>
                      <span data-text="link copied to clipboard">share verification link</span>
                    </button>
                    <div class="clearfix"></div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="share-block block">
                    <div class="pull-right hidden-sm">
                      <div class="inbound-link-preview">
                        <div style="padding:0;margin:0;overflow:hidden;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:bold;text-shadow:0 1px 1px rgba(255,255,255,0.6);width:180px;height:150px;display:block;position:relative;color:black!important;background-color:black!important">
                          <a target="_blank" href="javascript:void(0)" title="{{Auth::user()->name}}" style="padding:0;margin:0;overflow:hidden;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif">
                            <img src="{{smart_asset('assets/images/mr-square.jpg') }}" alt="Dubai Escorts - Massage Republic" style="padding:0;margin:0;overflow:hidden;display:block;position:relative;width:180px;height:150px;background-color:black!important">
                            <span style="padding:0;margin:0;overflow:hidden;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:bold;text-shadow:0 1px 1px rgba(255,255,255,0.6);display:block;position:absolute;top:0;left:0;width:180px;color:black!important">
                              <span style="padding:0;margin:0;overflow:hidden;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:bold;text-shadow:0 1px 1px rgba(255,255,255,0.6);display:block;text-align:center;color:black!important;font-size:21px!important">Featured Escort</span>
                              <span style="padding:0;margin:0;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:bold;text-shadow:0 1px 1px rgba(255,255,255,0.6);display:block;text-align:center;line-height:1;height:44px;overflow:hidden;color:black!important;font-size:21px!important">{{Auth::user()->name}}</span>
                            </span>
                          </a>
                          <a target="_blank" href="javascript:void(0)" title="Dubai Escort Service" style="padding:0;margin:0;overflow:hidden;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;font-weight:bold;position:absolute;width:178px;text-align:center;text-shadow:0 1px 1px rgba(255,255,255,0.6);display:block;text-transform:uppercase;text-decoration:underline;line-height:1.1;top:78px;color:black!important;font-size:13px!important">Dubai Escort</a>
                        </div>
                      </div>
                    </div>
                    <h3 class="no-margin-top border-bottom padding-bottom">Link to us</h3>
                    <p>Link to us from your site and get free credits.</p>
                    <a class="btn btn-primary" href="javascript:void(0)">Coming Soon</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-6"></div>
          </div>
          @if(session('show_upgrade_modal'))
          <div aria-hidden="true" aria-labelledby="upgradeCallLabel" class="modal" id="upgradeCall" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content m-3 m-sm-0">
                <div class="modal-header">
                  <h2 class="modal-title text-center pb-2" id="upgradeCallLabel">
                    <i class="fa fa-bullhorn fa-inline"></i> Get more attention!
                  </h2>
                </div>
                <div class="modal-body py-5">
                  <p class="lead">There are <span class="text-primary">6566 female escorts</span> listings in Dubai. </p>
                  <p class="lead mb-0">Increase your chance to be noticed. Upgrade your listing to&nbsp;a&nbsp;VIP profile for <span class="text-primary">less than $ 10 a day!</span>
                  </p>
                </div>
                <div class="modal-footer p-0">
                  <div class="d-flex justify-content-center">
                    <a class="btn btn-lg btn-primary px-5 mb-5" href="/my-profile/umer-329cee24-363b-4b1c-bf8f-a846d3b78c2e/upgrade/new?upgrade=premium" type="button">
                      <span class="pr-2">Upgrade Profile <i class="fa fa-arrow-right fa-inline"></i>
                      </span>
                    </a>
                  </div>
                  <div class="d-flex justify-content-end">
                    <button class="btn btn-sm btn-danger" data-dismiss="modal" type="button">Close</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>
    </div>
      @push('js')
        
        <script>
        $(document).ready(function() {
            // Initialize Bootstrap tooltips
            $('[data-toggle="tooltip"]').tooltip({
                trigger: 'hover',
                html: true
            });
            
            // Check for verification success from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('verification_success') === '1') {
                $('#success-message-text').text(' Verification photo uploaded successfully! Your photo is pending review.');
                $('#success-alert').show().fadeIn();
                
                // Remove the URL parameter without reloading
                const newUrl = window.location.pathname;
                window.history.replaceState({}, document.title, newUrl);
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('#success-alert').fadeOut();
                }, 5000);
            }
            
            // Check for success message from session storage
            const successMessage = sessionStorage.getItem('successMessage');
            console.log('Success message from storage:', successMessage);
            
            if (successMessage) {
                $('#success-message-text').text(successMessage);
                $('#success-alert').show().fadeIn();
                
                console.log('Alert should be visible now');
                
                // Clear the message from session storage
                sessionStorage.removeItem('successMessage');
                
                // Auto-hide after 5 seconds
                setTimeout(function() {
                    $('#success-alert').fadeOut();
                }, 5000);
            }
            
            // Show upgrade modal ONLY if session flag exists (after new profile creation)
            
            $('#upgradeCall').modal('show');
          
        });

        $(".my-listings-nav1").click(function(){
          $(".my-listings-nav").toggleClass("open");
        });

        // Handle Profile Status Toggle (Pause/Resume)
        $(document).on('click', '.profile-status-btn', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var profileId = $btn.data('profile-id');
            var action = $btn.data('action');
            var originalHtml = $btn.html();
            
            // Disable button and show loading
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin fa-inline"></i> Processing...');
            
            // Make AJAX request
            $.ajax({
                url: '/profile-status/' + profileId,
                method: 'POST',
                data: {
                    action: action,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Reload the page to show updated status
                    location.reload();
                },
                error: function(xhr) {
                    // Re-enable button and restore original text
                    $btn.prop('disabled', false).html(originalHtml);
                    alert('Error updating profile status. Please try again.');
                    console.error('Error:', xhr);
                }
            });
        });
        </script>
      @endpush
</div>