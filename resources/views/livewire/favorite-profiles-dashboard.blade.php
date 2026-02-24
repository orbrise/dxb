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
          <a href="{{ route('favorites.dashboard') }}">My Favorite Profiles</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

<!-- Success Message Alert -->
@if(session('success'))
<div class="alert alert-success alert-dismissible" style="position: fixed; top: 80px; right: 20px; z-index: 99999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 6px rgba(0,0,0,0.3);">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><i class="fa fa-check-circle"></i> Success!</strong>
    {{ session('success') }}
</div>
@endif

<style>
/* Stats Card */
.stats-card {
    background: #2a2a2a;
    border-radius: 10px;
    padding: 20px;
    text-align: center;
    margin-bottom: 20px;
}

.stats-card h3 {
    font-size: 28px;
    margin: 0 0 5px 0;
    color: #f4b827;
}

.stats-card p {
    margin: 0;
    color: #888;
    font-size: 14px;
}

/* Favorites Grid */
.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
}

.favorite-card {
    background: #2a2a2a;
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #333;
}

.favorite-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    border-color: #f4b827;
}

.favorite-card-body {
    display: flex;
    padding: 15px;
    gap: 15px;
}

.favorite-card-img {
    width: 100px;
    height: 120px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.favorite-card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.favorite-card-info {
    flex: 1;
    min-width: 0;
}

.favorite-card-name {
    font-size: 18px;
    font-weight: 600;
    color: #fff;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.favorite-card-name a {
    color: #fff;
    text-decoration: none;
}

.favorite-card-name a:hover {
    color: #f4b827;
}

.favorite-card-meta {
    color: #888;
    font-size: 13px;
    margin-bottom: 8px;
}

.favorite-card-meta i {
    color: #f4b827;
    margin-right: 5px;
}

.favorite-card-desc {
    color: #aaa;
    font-size: 13px;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.favorite-card-actions {
    display: flex;
    border-top: 1px solid #333;
}

.favorite-card-actions a,
.favorite-card-actions button {
    flex: 1;
    padding: 12px;
    background: transparent;
    border: none;
    color: #aaa;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: center;
    text-decoration: none;
}

.favorite-card-actions a:hover {
    background: #333;
    color: #f4b827;
}

.favorite-card-actions button:hover {
    background: rgba(220, 53, 69, 0.2);
    color: #dc3545;
}

.favorite-card-actions a + button {
    border-left: 1px solid #333;
}

.profile-views-badge {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    color: #f4b827;
    padding: 3px 8px;
    background-color: rgba(244, 184, 39, 0.15);
    border-radius: 4px;
    white-space: nowrap;
}

.package-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    color: #1a1a1a;
    padding: 3px 8px;
    background: linear-gradient(135deg, #f4b827 0%, #d4a017 100%);
    border-radius: 4px;
    text-transform: uppercase;
}

/* Empty State */
.favorites-empty {
    background: #2a2a2a;
    border-radius: 12px;
    padding: 60px 40px;
    text-align: center;
}

.favorites-empty i {
    font-size: 60px;
    color: #444;
    margin-bottom: 20px;
}

.favorites-empty h4 {
    color: #fff;
    margin-bottom: 10px;
}

.favorites-empty p {
    color: #888;
    margin-bottom: 20px;
}

.favorites-empty .btn {
    background: linear-gradient(135deg, #f4b827 0%, #d4a017 100%);
    border: none;
    color: #1a1a1a;
    padding: 12px 25px;
    border-radius: 25px;
    font-weight: 600;
    transition: transform 0.2s;
}

.favorites-empty .btn:hover {
    transform: scale(1.05);
}

/* Pagination */
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
    font-size: 14px;
    font-weight: 600;
    color: #aaa;
    background-color: #2a2a2a;
    border: 1px solid #333;
    text-decoration: none;
    transition: all 0.2s;
    border-radius: 4px;
}

.pagination li a:hover {
    background-color: #333;
    color: #f4b827;
    border-color: #f4b827;
}

.pagination li.active span {
    background: linear-gradient(135deg, #f4b827 0%, #d4a017 100%);
    color: #1a1a1a;
    border-color: #f4b827;
}

.pagination>li>a, .pagination>li>button, .pagination>li>span {
    font-size: 14px;
    height: 40px;
    line-height: 20px;
    padding: 10px 16px;
    background: #2a2a2a;
    border: 1px solid #333;
}

/* Alert */
.alert-success {
    background: rgba(40, 167, 69, 0.2);
    color: #28a745;
    border: 1px solid rgba(40, 167, 69, 0.3);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .favorites-grid {
        grid-template-columns: 1fr;
    }
    
    .favorite-card-body {
        padding: 12px;
        gap: 12px;
    }
    
    .favorite-card-img {
        width: 80px;
        height: 100px;
    }
    
    .favorite-card-name {
        font-size: 16px;
    }
}
</style>

<div class="container-fluid">
<div class="content-wrapper no-sidebar">
    <div id="content">
        @include('components.communication-nav')

        <div class="block my-profile-primary-block">
            <div class="row">
                <div class="col-md-12">
                    <div class="my-profile">
                        {{-- Stats Card --}}
                        <div class="stats-card">
                            <h3><i class="fa fa-heart"></i> {{ $favorites->total() }}</h3>
                            <p>Favorite Profiles</p>
                        </div>

                        @if($favorites->total() > 0)
                            <div class="favorites-grid">
                                @foreach($favorites as $favorite)
                                @php
                                    $profile = $favorite->profile;
                                @endphp
                                <div class="favorite-card">
                                    <div class="favorite-card-body">
                                        <div class="favorite-card-img">
                                            <a href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                                                @if(!empty($profile->coverimg->image))
                                                <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->coverimg->image)}}" />
                                                @elseif(!empty($profile->singleimg->image))
                                                <img alt="{{$profile->name}}" src="{{smart_asset('userimages/'.$profile->user_id.'/'.$profile->id.'/'.$profile->singleimg->image)}}" />
                                                @else
                                                <img alt="{{$profile->name}}" src="{{smart_asset('assets/images/default-avatar.png')}}" />
                                                @endif
                                            </a>
                                        </div>
                                        <div class="favorite-card-info">
                                            <div class="favorite-card-name">
                                                <a href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                                                    {{$profile->name}}
                                                </a>
                                                <span class="profile-views-badge">
                                                    <i class="fa fa-eye"></i> {{ $profile->profile_views ?? 0 }}
                                                </span>
                                                @if($profile->package_id && $profile->getpackage)
                                                <span class="package-badge">
                                                    {{ $profile->getpackage->name ?? 'PACKAGE' }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="favorite-card-meta">
                                                <i class="fa fa-map-marker-alt"></i> {{$profile->getcity->name}}
                                                <span class="mx-2">|</span>
                                                <i class="fa fa-venus"></i> {{$profile->ggender->name}}
                                            </div>
                                            @if(!empty($profile->about))
                                            <div class="favorite-card-desc">
                                                {{ \Str::limit($profile->about, 100) }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="favorite-card-actions">
                                        <a href="{{ url(strtolower($profile->ggender->name).'-escorts-in-'.strtolower($profile->getcity->name).'/'.$profile->id.'/'.$profile->slug) }}">
                                            <i class="fa fa-eye"></i> View Profile
                                        </a>
                                        <button wire:click="removeFavorite({{ $profile->id }})" type="button">
                                            <i class="fa fa-heart-broken"></i> Remove
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            @if($favorites->hasPages())
                            <div class="mt-4">
                                {{ $favorites->links() }}
                            </div>
                            @endif
                        @else
                            <div class="favorites-empty">
                                <i class="fa fa-heart"></i>
                                <h4>No favorites yet</h4>
                                <p>You haven't favorited any profiles yet. Browse escorts and click the bookmark icon to add them to your favorites.</p>
                                <a href="/female-escorts-in-dubai" class="btn">
                                    <i class="fa fa-search"></i> Browse Escorts
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@push('js')
<script>
$(document).ready(function() {
    setTimeout(function() {
        $('.alert-success').fadeOut();
    }, 5000);
});
</script>
@endpush
</div>