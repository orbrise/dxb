@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/my-account">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">Back to My Account</span>
      </a>
      <div class="title">
        <h1>Rejected Photo Verifications</h1>
      </div>
    </div>
  </div>
@endsection

<style>
.my-profile-disapprove {
    border-radius: 8px;
}

.block-reason {
    background-color: transparent;
    color: #fff;
    padding: 8px 20px;
    border-radius: 20px;
    border: 2px solid #e85d04;
    font-size: 14px;
    line-height: 1.5;
    text-align: center;
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
}

.badge-disapproved {
    background-color: #e85d04;
    color: #fff;
    font-weight: bold;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 4px;
    letter-spacing: 0.05ch;
}

.btn-fix-errors {
    background-color: #bb4623;
    color: #fff;
    border: none;
    font-weight: bold;
    padding: 10px 20px;
    border-radius: 4px;
    text-transform: none;
    width: 100%;
    max-width: 300px;
}

.btn-fix-errors:hover {
    color: #fff;
}

.rejection-aside {
    border-left: 2px solid #444;
    padding-left: 30px;
    width: 30%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

@media (max-width: 768px) {
    .rejection-aside {
        border-left: none;
        border-top: 2px solid #444;
        padding-left: 0;
        padding-top: 20px;
        margin-top: 20px;
    }
}

.listing-li .listing-info {
    height: auto;
    min-height: 130px;
}

/* Pagination Styles */
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



.my-profile .listing-li {
    padding: 20px;
}

.listing-li h2 .badge {
    background: #bb4623;

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

.nbtn {
    background: transparent;
    color: #aaaaaa;
    border-radius: 30px;
    border: 1px solid #4e4e4e;
    padding: 5px 25px;
}

.nbtn:hover {
    color: white;
}

@media (max-width: 576px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        margin-right: 0;
        margin-bottom: 8px;
        width: 100%;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
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
                                <a style="    padding: 6px 12px;" href="{{ route('user.dashboard', ['name' => $firstProfile->slug, 'id' => $firstProfile->id]) }}" 
                                   class="btn nbtn">
                                    <i class="fa fa-check-circle"></i> Active Profiles ({{ $activeCount ?? 0 }})
                                </a>
                                <a style="    padding: 6px 12px;" href="{{ route('rejected.verifications') }}" 
                                   class="btn btn-primary">
                                    <i class="fa fa-exclamation-triangle"></i> Rejected ({{ $rejectedCount ?? 0 }})
                                </a>
                                <a style="    padding: 6px 12px;" href="{{ route('user.dashboard', ['name' => $firstProfile->slug, 'id' => $firstProfile->id, 'filter' => 'pending']) }}" 
                                   class="btn nbtn">
                                    <i class="fa fa-clock"></i> Pending Approval ({{ $pendingCount ?? 0 }})
                                </a>
                                <a style="    padding: 6px 12px;" href="{{ route('profile.archived') }}" 
                                   class="btn nbtn">
                                    <i class="fa fa-archive"></i> Archived ({{ $archivedCount ?? 0 }})
                                </a>

                     
                            </div>
                                          <a style="color: #333; float:right; background: #f4b827 linear-gradient(#f4b827, #d3980b) repeat-x;
    border-color: #c9910a;" class="btn my-listing-new-link " href="{{ route('new.profile') }}" data-turbolinks="false">
              Add Profile <i class="fa fa-plus fa-text-default"></i>
          </a>

                        </div> --}}

            <div class="block my-profile-primary-block">
                <div class="row">
                    <div class="col-md-12">
                        

                        <!-- Profile Filter Links -->
                        @if($firstProfile)
                        
                        @endif

                        @if(isset($rejectedProfiles) && $rejectedProfiles->total() > 0)
                            @foreach($rejectedProfiles as $profile)
                            <div class="my-profile my-profile-disapprove mb-4">
                                <div class="d-md-flex gap-4 pb-3">
                                    <div class="listing-li pb-3 pr-2 position-relative free thumbs-0 thumbs-free">
                                        <h2 class="visible-xxs">
                                            {{$profile->name}} 
                                            <span class="badge badge-disapproved">disapproved</span>
                                        </h2>
                                        <div class="thumbs">
                                            <div class="main-thumbs">
                                                <span class="img-wrapper basic">
                                                    <div class="image-wrapper">
                                                        @if(!empty($profile->coverimg->image))
                                                        <img alt="{{$profile->name}}" class="img-responsive" height="95" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" width="89" />
                                                        @elseif(!empty($profile->singleimg->image))
                                                        <img alt="{{$profile->name}}" class="img-responsive" height="95" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" width="89" />
                                                        @else
                                                        <img alt="{{$profile->name}}" class="img-responsive" height="95" src="{{smart_asset('assets/images/default-avatar.png')}}" width="89" />
                                                        @endif
                                                    </div>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="listing-info-wrapper">
                                            <div class="listing-info">
                                                <h2>
                                                    {{$profile->name}} 
                                                    <span class="badge badge-disapproved">disapproved</span>
                                                </h2>
                                                <p class="text-muted">
                                                    <i class="fa fa-map-marker-alt fa-inline"></i> {{$profile->getcity->name}}
                                                    <span class="mx-2">|</span>
                                                    <i class="fa fa-venus fa-inline"></i> {{$profile->ggender->name}}
                                                </p>
                                                @if(!empty($profile->about))
                                                <p class="desc">{{\Str::limit($profile->about, $limit=130, $end='...')}}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <aside class="rejection-aside">
                                        <div class="block-reason my-3">
                                            {{ $profile->rejectedVerification->rejection_reason ?? 'Use your own photos' }}
                                        </div>
                                        @if($profile->rejectedVerification->rejection_link)
                                            <a class="btn btn-fix-errors fw-bold mb-2" href="{{ $profile->rejectedVerification->rejection_link }}" target="_blank">
                                                <i class="fa fa-external-link-alt fa-inline"></i> Fix Errors
                                            </a>
                                        @else
                                            <a class="btn btn-fix-errors fw-bold mb-2" href="{{url('my-profile/'.$profile->slug.'/'.$profile->id.'/verify-photo')}}">
                                                <i class="fa fa-pencil-alt fa-inline"></i> Fix Errors
                                            </a>
                                        @endif
                                        <small class="text-muted text-center">
                                            Rejected {{ $profile->rejectedVerification->created_at->diffForHumans() }}
                                        </small>
                                    </aside>
                                </div>
                                <div class="border-top py-3 d-flex justify-content-center justify-content-md-between align-items-center">
                                    <a class="btn btn-default d-flex align-items-center" href="/support" style="text-shadow:none">
                                        <span class="mr-2 fw-bold">Need help?</span>
                                        <svg width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                                            <path fill="currentColor" d="M367.2 412.5C335.9 434.9 297.5 448 256 448s-79.9-13.1-111.2-35.5l58-58c15.8 8.6 34 13.5 53.3 13.5s37.4-4.9 53.3-13.5l58 58zm90.7 .8c33.8-43.4 54-98 54-157.3s-20.2-113.9-54-157.3c9-12.5 7.9-30.1-3.4-41.3S425.8 45 413.3 54C369.9 20.2 315.3 0 256 0S142.1 20.2 98.7 54c-12.5-9-30.1-7.9-41.3 3.4S45 86.2 54 98.7C20.2 142.1 0 196.7 0 256s20.2 113.9 54 157.3c-9 12.5-7.9 30.1 3.4 41.3S86.2 467 98.7 458c43.4 33.8 98 54 157.3 54s113.9-20.2 157.3-54c12.5 9 30.1 7.9 41.3-3.4s12.4-28.8 3.4-41.3zm-45.5-46.1l-58-58c8.6-15.8 13.5-34 13.5-53.3s-4.9-37.4-13.5-53.3l58-58C434.9 176.1 448 214.5 448 256s-13.1 79.9-35.5 111.2zM367.2 99.5l-58 58c-15.8-8.6-34-13.5-53.3-13.5s-37.4 4.9-53.3 13.5l-58-58C176.1 77.1 214.5 64 256 64s79.9 13.1 111.2 35.5zM157.5 309.3l-58 58C77.1 335.9 64 297.5 64 256s13.1-79.9 35.5-111.2l58 58c-8.6 15.8-13.5 34-13.5 53.3s4.9 37.4 13.5 53.3zM208 256a48 48 0 1 1 96 0 48 48 0 1 1 -96 0z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @endforeach
                            
                            <!-- Pagination -->
                            @if($rejectedProfiles->hasPages())
                            <div class="mt-4">
                                {{ $rejectedProfiles->links() }}
                            </div>
                            @endif
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>Good news!</strong> You don't have any rejected photo verifications at the moment.
                                <br><br>
                                <a href="/my-account" class="btn btn-primary">
                                    <i class="fa fa-arrow-left"></i> Back to My Account
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
