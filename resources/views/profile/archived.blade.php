@extends('components.layouts.app')

@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/female-escorts-in-dubai">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">Escorts in Dubai</span>
      </a>
      <div class="title">
        <h1>
          <a href="javascript:history.back()">Archived Profiles</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

@section('content')
<style>
.listing-li .listing-info {
    height: 130px;
}

.my-profile .listing-li {
    border-bottom: 1px solid #444444;
    padding-bottom: 20px;
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

.margin-right {
    margin-right: 8px !important;
}

/* Archived Badge */
.archived-badge {
    display: inline-block;
    font-size: 14px;
    font-weight: normal;
    color: #ff6b6b;
    padding: 4px 10px;
    background-color: rgba(255, 107, 107, 0.1);
    border-radius: 4px;
    border: 1px solid rgba(255, 107, 107, 0.3);
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
            @if(session('error'))
            <div class="alert alert-danger" role="alert">
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
                <i class="fa fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif
          </div>

          {{-- <div class="mb-4">
                    <div class="btn-group" role="group">
                      <a style="padding: 6px 12px;" href="javascript:history.back()" 
                         class="btn nbtn">
                        <i class="fa fa-check-circle"></i> Active Profiles ({{ $activeCount ?? 0 }})
                      </a>
                      <a style="padding: 6px 12px;" href="{{ route('rejected.verifications') }}" 
                         class="btn nbtn">
                        <i class="fa fa-exclamation-triangle"></i> Rejected ({{ $rejectedCount ?? 0 }})
                      </a>
                      <a style="padding: 6px 12px;" href="javascript:history.back()" 
                         class="btn nbtn">
                        <i class="fa fa-clock"></i> Pending Approval ({{ $pendingCount ?? 0 }})
                      </a>
                      <a style="padding: 6px 12px;" href="{{ route('profile.archived') }}" 
                         class="btn btn-primary">
                        <i class="fa fa-clock"></i> Archived ({{ $archivedCount ?? 0 }})
                      </a>
                    </div>
                     <a style="color: #333; float:right; background: #f4b827 linear-gradient(#f4b827, #d3980b) repeat-x;
    border-color: #c9910a;" class="btn my-listing-new-link " href="{{ route('new.profile') }}" data-turbolinks="false">
              Add Profile <i class="fa fa-plus fa-text-default"></i>
          </a>
          </div> --}}
          @include('components.profile-dashboard-nav')
          <div class="block my-profile-primary-block">
            <div class="row">
              <div class="col-md-12">
                <div class="my-profile">
                 
                  
                  @if($archivedProfiles->count() > 0)
                    @foreach($archivedProfiles as $profile)
                    <div class="listing-li free thumbs-0 thumbs-free mb-3" style="opacity: 0.7;">
                      <h2 class="visible-xxs">
                        <a class="nostyle-link" href="javascript:void(0)" title="{{$profile->name}}, escort in {{$profile->getcity->name ?? 'Unknown'}}">
                          {{$profile->name}}
                          <span class="archived-badge ml-2">
                            <i class="fa fa-archive"></i> Archived
                          </span>
                        </a>
                      </h2>
                      <div class="thumbs">
                        <div class="main-thumbs">
                          <a class="img pb-photo-link" href="javascript:void(0)">
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
                          </a>
                        </div>
                        @if($profile->archived_at)
                        <div class="package-expiry" style="color: #ff6b6b;text-align: center;
    clear: both;">
                            <i class="fa fa-calendar"></i>
                            <span>Archived:<br> {{ \Carbon\Carbon::parse($profile->archived_at)->diffForHumans() }}</span>
                        </div>
                        @endif
                      </div>
                      <div class="listing-info-wrapper">
                        <div class="listing-info">
                          <h2>
                            <a class="nostyle-link" href="javascript:void(0)" title="{{$profile->name}}, escort in {{$profile->getcity->name ?? 'Unknown'}}">
                              {{$profile->name}}
                              <span class="archived-badge ml-2">
                                <i class="fa fa-clock"></i> Archived
                              </span>
                            </a>
                          </h2>
                          <a class="nostyle-link" href="javascript:void(0)">
                            <p class="text-muted">
                              <i class="fa fa-map-marker-alt fa-inline"></i> {{$profile->getcity->name ?? 'Unknown'}}
                              <span class="mx-2">|</span>
                              <i class="fa fa-venus fa-inline"></i> {{$profile->ggender->name ?? 'Unknown'}}
                            </p>
                            @if($profile->archive_reason)
                            <p class="text-muted">
                                <i class="fa fa-info-circle"></i> Reason: {{ $profile->archive_reason }}
                            </p>
                            @endif
                            @if(!empty($profile->about))
                            <p>{{\Str::limit($profile->about, $limit=130, $end='...')}}</p>
                            @endif
                          </a>
                        </div>
                        <div class="my-listing-actions">
                          <form action="{{ route('profile.reactivate', $profile->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to repost this profile?')"
                                    class="nbtn margin-right">
                                <i class="fa fa-refresh fa-inline"></i> Repost
                            </button>
                          </form>
                          <a class="btn nbtn margin-right" href="{{url('my-profile/'.$profile->slug.'/'.$profile->id)}}">
                            <i class="fa fa-eye fa-inline"></i>View
                          </a>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  @else
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> You don't have any archived profiles.
                    </div>
                  @endif
                  
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
@endsection
