@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">Back</span>
      </a>
      <div class="title">
        <h1>
          <a href="/auctions/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}">Auctions for Top Spots in {{ $cityName }}</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

@push('css')
<style>
  .auction-cover {
    background-color: rgba(0, 0, 0, 0.7);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    color: white;
    z-index: 10;
  }
  .spot {
    position: relative;
  }
  .spot-img-wrapper {
    height: 220px;
    overflow: hidden;
  }
  .main-img {
    width: 100%;
    object-fit: cover;
  }
  .grid-col-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  @media (min-width: 768px) {
    .grid-sm-col-3 {
      grid-template-columns: repeat(3, 1fr);
    }
  }
</style>
@endpush

<div class="container-fluid">
    @if(!$canBid)
    <div class="flash-notice alert alert-dismissible alert-warning">
      <button aria-hidden="" class="close" data-dismiss="alert" type="button"><i class="fa fa-times-circle"></i></button>
      <div id="flash_error">To access auctions, you must purchase or spend at least 300 credits that you purchased.</div>
    </div>
    @endif
    
    <div class="content-wrapper no-sidebar">
        <div id="content">
          <div class="row">
            <section class="listings-spots">
              <h3 class="pb-2 pl-3 mt-3 mx-auto">
                <span class="pr-2">This week's deluxe profiles in {{ $cityName }}</span>
                <span class="small d-inline-block">(view for logged-in advertisers)</span>
              </h3>
              
              <div class="d-grid grid-col-2 grid-sm-col-3 px-2 px-lg-3 mx-auto">
                @forelse($auctions as $auction)
                <div class="spot mb-5">
                  <div class="auction-cover d-flex flex-column justify-content-between" 
                       style="@if($auction->highestBidderProfile && !empty($auction->highestBidderProfile->singleimg)) 
                              background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ smart_asset('storage/userimages/'.$auction->highestBidderProfile->user_id.'/'.$auction->highestBidderProfile->id.'/'.$auction->highestBidderProfile->singleimg->image) }}'); 
                              background-size: cover; 
                              background-position: center;
                              @elseif($auction->featuredProfile && !empty($auction->featuredProfile->singleimg))
                              background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('{{ smart_asset('storage/userimages/'.$auction->featuredProfile->user_id.'/'.$auction->featuredProfile->id.'/'.$auction->featuredProfile->singleimg->image) }}'); 
                              background-size: cover; 
                              background-position: center;
                              @endif">
                    <div>
                      <h3 class="text-center mb-0 mt-5">${{ $auction->current_price}} </h3>
                      <p class="small text-center">for {{ $auction->duration_days ?? 7 }} days</p>
                    </div>
                    <a class="btn btn-primary px-4 mb-4 mx-auto" href="/auctions/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}/spot/{{ $auction->spot_number }}">Make offer</a>
                    <div>
                      <div class="d-flex align-items-center justify-content-center">
                        <img alt="Timer icon" class="mr-2" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/timer-5fc5fc1474905d451c5cb2d9ad472d17fea1e9059c0baf436d0aaf6df2b2aeed.svg" width="24" />
                        <span>auction ends in:</span>
                      </div>
                      <p class="text-center">{{ $auction->daysLeft }} {{ Str::plural('day', $auction->daysLeft) }}</p>
                    </div>
                  </div>
                  
                  <div class="card d-flex">
                    @if($auction->highestBidderProfile)
                   
                    <a class="d-block spot-img-wrapper" href="/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}/{{ $auction->highestBidderProfile->id }}/{{ $auction->highestBidderProfile->slug }}">
                      @if(!empty($auction->highestBidderProfile->singleimg))
                          <img class="main-img" height="220" loading="lazy" src="{{ asset('storage/userimages/'.$auction->highestBidderProfile->user_id.'/'.$auction->highestBidderProfile->id.'/'.$auction->highestBidderProfile->singleimg->image) }}" />
                      @else
                          <img class="main-img" height="220" loading="lazy" src="https://via.placeholder.com/240x300" />
                      @endif
                  </a>
                  <div class="card-body px-2">
                      <h3 class="m-0 px-2 py-3">
                          <a href="/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}/{{ $auction->highestBidderProfile->id }}/{{ $auction->highestBidderProfile->slug }}">{{ $auction->highestBidderProfile->name }}</a>
                      </h3>
                  </div>
                    @elseif($auction->featuredProfile)
                   
                      <a class="d-block spot-img-wrapper" href="/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}/{{ $auction->featuredProfile->id }}/{{ $auction->featuredProfile->slug }}">
                        <img class="main-img" height="220" loading="lazy" src="{{smart_asset('storage/userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image) }}" />
                      </a>
                      <div class="card-body px-2">
                        <h3 class="m-0 px-2 py-3">
                          <a href="/{{ $gender }}-escorts-in-{{ strtolower($cityName) }}/{{ $auction->featuredProfile->id }}/{{ $auction->featuredProfile->slug }}">{{ $auction->featuredProfile->title }}</a>
                        </h3>
                      </div>
                    @else
                      
                      <div class="card-body px-2">
                        <h3 class="m-0 px-2 py-3"></h3>
                      </div>
                    @endif
                  </div>
                  <div class="spot-id text-center py-3 text-uppercase small">Spot #{{ $auction->spot_number }}</div>
                </div>
                @empty
                <div class="col-12 text-center py-5">
                  <h3>No active auctions found for {{ $gender }} escorts in {{ $cityName }}</h3>
                  <p>Check back later or contact support for more information.</p>
                </div>
                @endforelse
              </div>
            </section>
            <hr class="pb-3" />
          </div>
        </div>
    </div>
</div>