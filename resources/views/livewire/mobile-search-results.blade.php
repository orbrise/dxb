@push('css')
<style>
  @media (max-width: 567px) {
    .listings>.listing-li.basic, .listings>.listing-li.free {
        width: 100%;
        display: inline-block;
    }
}

@media (max-width: 567px) {
    .listing-li>.listing-info-wrapper>.listing-info {
        display: block;
    }
    
    /* Hide the name that appears after the image on mobile - only show the name before image */
    .listing-info h2 {
        display: none !important;
    }
}

.listing-li .img-wrapper.premium img {
    width: 200px;
    height: 221px;
    object-fit: cover;
}

/* Loader/spinner styling */
.loader,
[wire\:loading],
.loading-spinner,
img[src*="loading"],
.spinner-border,
.spinner {
    width: 24px !important;
    height: 24px !important;
    border-color: #f4b827 !important;
    border-right-color: transparent !important;
}

/* Livewire loading indicator */
[wire\:loading].spinner-border {
    width: 24px !important;
    height: 24px !important;
    border-width: 2px !important;
    border-color: #f4b827 !important;
    border-right-color: transparent !important;
}

/* Generic loading circle/spinner */
.loading,
.lazy-load-placeholder::before,
.img-loading::after {
    border-color: #f4b827 !important;
    border-right-color: transparent !important;
}
  </style>
@endpush

<div class="">
    <!-- Back to search button -->
    <div class="container py-4">
    
        <a class="btn btn-dark btn-block" href="{{ route('mobile.search', ['gender' => $gender, 'city' => $selectedcity ?? 'Dubai']) }}" tabindex="3">
          <i class="fa fa-search fa-fw"></i>Search for {{ ucfirst($gender) }} escorts 
        </a>
    </div>
    
    
    
    <!-- Profiles list -->
    <div class="container-fluid">
    <div class="content-wrapper no-sidebar">
      <div id="content">

    <div class="col-md-9 col-xs-12" style="padding:0px">
      <h2>{{ ucfirst($gender) }} Escorts in {{ ucfirst($selectedcity) }}</h2>
      <p class="page-desc margin-bottom hidden-xs">We have 6681 Dubai escorts on Massage Republic, 4711 profiles have verified photos. <span class="services">The most popular services offered are: <a href="/massage-female-escorts-in-dubai" title="Erotic Massage Escorts in Dubai">Massage</a>, <a href="/oral-sex-blowjob-female-escorts-in-dubai" title="Oral sex - blowjob Escorts in Dubai">Oral sex - blowjob</a>, <a href="/cob-come-on-body-female-escorts-in-dubai" title="COB - Come On Body Escorts in Dubai">COB - Come On Body</a>, <a href="/french-kissing-female-escorts-in-dubai" title="French kissing Escorts in Dubai">French kissing</a>, <a href="/owo-oral-w-o-condom-female-escorts-in-dubai" title="OWO - Oral without condom Escorts in Dubai">OWO - Oral without condom</a>, <a href="/gfe-female-escorts-in-dubai" title="Girlfriend Experience Escorts in Dubai">GFE</a>, <a href="/deep-throat-female-escorts-in-dubai" title="Deep throat Escorts in Dubai">Deep throat</a>, and <a href="/foot-fetish-female-escorts-in-dubai" title="Foot fetish Escorts in Dubai">Foot fetish</a>. </span> Prices range from 44 AED to 30,001 AED <span class="usd-price text-muted">(US$ 11 to US$ 8,167)</span>, the average cost advertised is 1,149 AED <span class="usd-price text-muted">(US$ 312)</span>. We also have listings nearby in <a title="Female Escorts in Abu Dhabi" href="/female-escorts-in-abu-dhabi">Abu Dhabi</a>, <a title="Female Escorts in Ajmān" href="/female-escorts-in-ajman">Ajmān</a>, <a title="Female Escorts in Al Ain" href="/female-escorts-in-al-ain">Al Ain</a>, <a title="Female Escorts in Fujairah" href="/female-escorts-in-fujairah">Fujairah</a>, <a title="Female Escorts in Kalba" href="/female-escorts-in-kalba">Kalba</a>, <a title="Female Escorts in Ras al-Khaimah" href="/female-escorts-in-ras-al-khaimah">Ras al-Khaimah</a>, <a title="Female Escorts in Sharjah" href="/female-escorts-in-sharjah">Sharjah</a>, and <a title="Female Escorts in Umm al-Qaiwain" href="/female-escorts-in-umm-al-qaiwain">Umm al-Qaiwain</a>. </p>      <div class="listings listings-spots listing-spots--minimal border-top padding-top mx-n2 mx-sm-0">
        
     
       @if($auctions->count() > 0)
<div class="listings listings-spots listing-spots--minimal border-bottom mx-n2 mx-sm-0">
  @foreach($auctions as $auction)
  <div class="spot">
    {{-- Only show the auction bidding overlay for active auctions AND logged in users --}}
    @if($auction->status == 'active' && Auth::check())
    <div class="auction-cover d-flex align-items-center flex-wrap">
      <div class="d-flex flex-column align-items-center align-self-start justify-content-between flex-sidebar">
        <div class="spot-id pt-3 px-3 pb-2 text-uppercase small">Spot&nbsp;#{{ $auction->spot_number }}</div>
        <details class="ml-3 w-100 d-none d-sm-block" data-popover="up">
          <summary class="d-flex align-items-center pb-2">
            <img alt="Question mark icon" class="mr-2" height="18" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/query-9f855724e9abf46e4a04ed35fbe5d2b97780f89950dd709df860db1f6b3c04e3.svg" width="18">
            <span class="text-uppercase">Why is this the best choice?</span>
          </summary>
          <div class="popover-content p-2 mb-0">Spots stay always at the top! These profiles are first to be seen.</div>
        </details>
      </div>
      <div class="position-relative flex-not-sidebar">
        <div class="d-flex flex-column align-items-center">
          <h3 class="mb-4">
            <span>Current price for {{ $auction->duration_days ?? 7 }} days:</span>
            <strong class="ml-2">€{{ $auction->current_price }}</strong>
          </h3>
          <a class="btn btn-primary px-5 mb-3 font-weight-bold" href="/auctions/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/spot/{{ $auction->spot_number }}" style="font-size:1rem">Make offer</a>
        </div>
        <div class="d-flex align-items-center justify-content-center">
          <img alt="Timer icon" class="mr-2" height="28" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/timer-5fc5fc1474905d451c5cb2d9ad472d17fea1e9059c0baf436d0aaf6df2b2aeed.svg" width="28">
          <p class="mb-0">auction ends in: {{ $auction->daysLeft }} {{ Str::plural('day', $auction->daysLeft) }}</p>
        </div>
      </div>
    </div>
    @endif
            
            <div class="listing-li listing-li--spot premium thumbs-3 thumbs-mini p-3">
              <h2 class="visible-xxs">
                @if($auction->status == 'ended' && $auction->winnerProfile)
                  {{-- For ended auctions with winners, show the winner profile --}}
                  <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                    {{ $auction->winnerProfile->name }}
                    @if($auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                        <i class="fa fa-heart2"></i>
                        <span>{{ $auction->winnerProfile->reviews->count() }}</span>
                      </span>
                    @endif
                    @if($auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                        <i class="fa fa-question-circle"></i>
                        <span>{{ $auction->winnerProfile->questions->count() }}</span>
                      </span>
                    @endif
                  </a>
                @elseif($auction->status == 'active')
                  {{-- For active auctions, show the current profile or "Available Spot" --}}
                  <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                    {{ $auction->winnerProfile->name ?? 'Available Spot' }}
                    @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                        <i class="fa fa-heart2"></i>
                        <span>{{ $auction->winnerProfile->reviews->count() }}</span>
                      </span>
                    @endif
                    @if(isset($auction->winnerProfile) && $auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                      <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                        <i class="fa fa-question-circle"></i>
                        <span>{{ $auction->winnerProfile->questions->count() }}</span>
                      </span>
                    @endif
                  </a>
                @endif
              </h2>
              
              <div class="thumbs">
                <div class="main-thumbs">
                  @if($auction->status == 'ended' && $auction->winnerProfile)
                    {{-- For ended auctions with winners, link to the winner profile --}}
                    <a class="img pb-photo-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                      <span class="img-wrapper premium">
                        @if($auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                          <span class="verified-image text-left small" title="Photos Verified">
                            <i class="fa fa-check"></i>
                            <span>Verified photos</span>
                          </span>
                        @endif
                        <div class="image-wrapper">
                          @if(!empty($auction->winnerProfile->coverimg))
                            <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }}" class="img-responsive" height="208" 
                              src="{{ asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->coverimg->image) }}" width="200">
                          @elseif(!empty($auction->winnerProfile->singleimg))
                            <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }}" class="img-responsive" height="208" 
                              src="{{ asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image) }}" width="200">
                          @endif
                        </div>
                      </span>
                    </a>
                  @else
                    {{-- For active auctions, show current profile or placeholder --}}
                    <a class="img pb-photo-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                      <span class="img-wrapper premium">
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                          <span class="verified-image text-left small" title="Photos Verified">
                            <i class="fa fa-check"></i>
                            <span>Verified photos</span>
                          </span>
                        @endif
                        <div class="image-wrapper">
                          @if(isset($auction->winnerProfile) && !empty($auction->winnerProfile->coverimg))
                            <img alt="{{ $auction->winnerProfile->name ?? 'Escort' }} - escort in {{ $selectedcity }}" class="img-responsive" height="208" 
                              src="{{ asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->coverimg->image) }}" width="200">
                          @elseif(isset($auction->winnerProfile) && !empty($auction->winnerProfile->singleimg))
                            <img alt="{{ $auction->winnerProfile->name ?? 'Escort' }} - escort in {{ $selectedcity }}" class="img-responsive" height="208" 
                              src="{{ asset('userimages/'.$auction->winnerProfile->user_id.'/'.$auction->winnerProfile->id.'/'.$auction->winnerProfile->singleimg->image) }}" width="200">
                          @else
                            <img alt="Available Spot" class="img-responsive" height="208" src="https://via.placeholder.com/200x208" width="200">
                          @endif
                        </div>
                      </span>
                    </a>
                  @endif
                </div>
                
                <div class="other-thumbs pull-left">
                  @if($auction->status == 'ended' && $auction->winnerProfile && $auction->winnerProfile->multipleimgs)
                    {{-- For ended auctions with winners, show the winner's images --}}
                    @foreach($auction->winnerProfile->multipleimgs->take(3) as $key => $img)
                      <div class="thumb thumb-{{ $key }}">
                        <a class="img img-responsive pb-photo-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                          <span class="img-wrapper mini">
                            @if($auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                              <span class="verified-image text-left small" title="Photos Verified">
                                <i class="fa fa-check"></i>
                                <span>Verified photos</span>
                              </span>
                            @endif
                            <div class="image-wrapper">
                              <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }} Photo {{ $key+1 }}" class="img-responsive" height="60" 
                                src="{{ asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}" width="60">
                            </div>
                          </span>
                        </a>
                      </div>
                    @endforeach
                  @elseif(isset($auction->winnerProfile) && $auction->winnerProfile->multipleimgs)
                    {{-- For active auctions with a profile, show the profile's images --}}
                    @foreach($auction->winnerProfile->multipleimgs->take(3) as $key => $img)
                      <div class="thumb thumb-{{ $key }}">
                        <a class="img img-responsive pb-photo-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                          <span class="img-wrapper mini">
                            @if($auction->winnerProfile->photoverify && $auction->winnerProfile->photoverify->status == 'approved')
                              <span class="verified-image text-left small" title="Photos Verified">
                                <i class="fa fa-check"></i>
                                <span>Verified photos</span>
                              </span>
                            @endif
                            <div class="image-wrapper">
                              <img alt="{{ $auction->winnerProfile->name }} - escort in {{ $selectedcity }} Photo {{ $key+1 }}" class="img-responsive" height="60" 
                                src="{{ asset('userimages/'.$img->user_id.'/'.$img->profile_id.'/'.$img->image) }}" width="60">
                            </div>
                          </span>
                        </a>
                      </div>
                    @endforeach
                  @endif
                </div>
              </div>
      
              <div class="listing-info-wrapper">
                <div class="listing-info">
                  <h2>
                    @if($auction->status == 'ended' && $auction->winnerProfile)
                      {{-- For ended auctions with winners, link to the winner profile --}}
                      <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                        {{ $auction->winnerProfile->name }}
                        @if($auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                            <i class="fa fa-heart2"></i>
                            <span>{{ $auction->winnerProfile->reviews->count() }}</span>
                          </span>
                        @endif
                        @if($auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                            <i class="fa fa-question-circle"></i>
                            <span>{{ $auction->winnerProfile->questions->count() }}</span>
                          </span>
                        @endif
                      </a>
                    @else
                      {{-- For active auctions, show current profile or "Available Spot" --}}
                      <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                        {{ $auction->winnerProfile->name ?? 'Available Spot' }}
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->reviews && $auction->winnerProfile->reviews->count() > 0)
                          <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->reviews->count() }} approved reviews">
                            <i class="fa fa-heart2"></i>
                            <span>{{ $auction->winnerProfile->reviews->count() }}</span>
                          </span>
                        @endif
                        @if(isset($auction->winnerProfile) && $auction->winnerProfile->questions && $auction->winnerProfile->questions->count() > 0)
                        <span class="badge" data-placement="top" data-toggle="tooltip" title="{{ $auction->winnerProfile->name }} has answered {{ $auction->winnerProfile->questions->count() }} questions">
                          <i class="fa fa-question-circle"></i>
                          <span>{{ $auction->winnerProfile->questions->count() }}</span>
                        </span>
                      @endif
                    </a>
                  @endif
                </h2>
                
                @if($auction->status == 'ended' && $auction->winnerProfile)
                  {{-- For ended auctions with winners, show the winner's profile content --}}
                  <a class="nostyle-link" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                    <p>{{ str()->of($auction->winnerProfile->about)->limit(150) }}</p>
                  </a>
                  <p class="no-margin see-more">
                    <a class="btn btn-dark" href="/{{ $gender }}-escorts-in-{{ strtolower($selectedcity) }}/{{ $auction->winnerProfile->id }}/{{ $auction->winnerProfile->slug }}">
                      See more & contact
                    </a>
                  </p>
                @else
                  {{-- For active auctions, show auction information --}}
                  <a class="nostyle-link" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                    <p>This spot is available for auction. Place your bid to feature your profile here and get maximum visibility!</p>
                  </a>
                  <p class="no-margin see-more">
                    <a class="btn btn-dark" href="{{ Auth::check() ? '/auctions/'.$gender.'-escorts-in-'.strtolower($selectedcity).'/spot/'.$auction->spot_number : '/sign-in' }}">
                      {{ Auth::check() ? 'Make a bid' : 'Sign in to bid' }}
                    </a>
                  </p>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @endif
        
        {{-- <div class="spot">
          <div class="listing-li listing-li--spot premium thumbs-3 thumbs-mini p-3">
            <h2 class="visible-xxs">
              <a class="nostyle-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198" title="Anastasia, Belarusian escort agency in Dubai (14)">Anastasia</a>
            </h2>
            <div class="thumbs">
              <div class="main-thumbs">
                <a class="img pb-photo-link" href="">
                  <span class="img-wrapper premium">
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    <div class="image-wrapper">
                      <img alt="Anastasia - escort agency in Dubai Photo 10 of 10" class="img-responsive" height="208" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728120_premium.jpg" width="200" />
                    </div>
                  </span>
                </a>
              </div>
              <div class="other-thumbs pull-left">
                <div class="thumb thumb-0">
                  <a class="img img-responsive pb-photo-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="Anastasia - escort agency in Dubai Photo 1 of 10" class="img-responsive" height="60" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728102_mini.jpg" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
                <div class="thumb thumb-1">
                  <a class="img img-responsive pb-photo-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="Anastasia - escort agency in Dubai Photo 2 of 10" class="img-responsive" height="60" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728104_mini.jpg" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
                <div class="thumb thumb-2">
                  <a class="img img-responsive pb-photo-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="Anastasia - escort agency in Dubai Photo 3 of 10" class="img-responsive" height="60" src="https://d18fr84zq3fgpm.cloudfront.net/anastasia-belarusian-escort-in-dubai-9728106_mini.jpg" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
              </div>
            </div>
            <div class="listing-info-wrapper">
              <div class="listing-info">
                <h2>
                  <a class="nostyle-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198" title="Anastasia, Belarusian escort agency in Dubai (14)">Anastasia</a>
                </h2>
                <a class="nostyle-link" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">
                  <p>Juicy and colorful Anastasia is already in Dubai! Her forms will definitely give you a sensation if you know what😉She knows the true desires of men and fulfills them with all the love you want. Also with her it is pleasant to communicate and share all your feelings, she will listen and will not judge, Come to her for peace of mind and thrill in bed🔥🔥🔥</p>
                </a>
                <p class="no-margin see-more">
                  <a class="btn btn-dark" href="/female-escorts-in-dubai/anastasia-fc669868-ea59-4164-884f-a1e4e1a79198">See more &amp; contact</a>
                </p>
              </div>
            </div>
          </div>
        </div> --}}
      </div>
      <div class="listings  @if($auctions->count() > 0 and Auth::check()) padding-top @endif">
        @forelse($profiles as $profile)
        @if($profile->package_id >= 21 || $profile->package_id == 28 || $profile->package_id == 26)
        <div class="listing-li premium thumbs-3 thumbs-mini">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="/female-escorts-in-dubai/lea-ukrainian" title="Lea, Ukrainian escort in Dubai (3)">{{$profile->name}} <span class="badge" data-placement="top" data-toggle="tooltip" title="One review. Rating: ❤❤❤❤❤">
                <i class="fa fa-heart2"></i>
                <span>1</span>
              </span>
            </a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper premium">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
                    @if(!empty($profile->coverimg->image))
                    <img alt="" class="img-responsive" height="208" src="{{smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->coverimg->image)}}" width="200" />
                 @else
                 <img alt="" class="img-responsive" height="208" src="{{smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->singleimg->image)}}" width="200" />

                    @endif
                  </div>
                </span>
              </a>
            </div>

            
            <div class="other-thumbs pull-left">
              @forelse($profile->multipleimgss as $imgs)
              <div class="thumb thumb-0">
                <a class="img img-responsive pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                  <span class="img-wrapper mini">
                 
                    @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    @endif
                    <div class="image-wrapper">
                      <img alt="" class="img-responsive" height="60" src="{{smart_asset("userimages/".$imgs->user_id."/".$imgs->profile_id."/".$imgs->image)}}" width="60" />
                    </div>
                  </span>
                </a>
              </div>
              @empty
                  
              @endforelse
              
              
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}" title="Lea, Ukrainian escort in Dubai (3)">{{$profile->name}} 
                  @if($profile->reviews->count() > 0)
                  <span class="badge" data-placement="top" data-toggle="tooltip" title="{{$profile->reviews->count()}} Reviews">
                  <i class="fa fa-heart2"></i>
                    <span>{{ $profile->reviews->count() }}</span>
                  </span>
                  @endif

                  {{-- <a href="#" wire:click.prevent="toggleFavorite({{ $profile->id }})" class="btn btn-sm {{ $this->checkIfFavorited($profile->id) ? 'btn-danger' : 'btn-outline-danger' }}">
                    <i class="fa {{ $this->checkIfFavorited($profile->id) ? 'fa-heart' : 'fa-heart-o' }}"></i>
                </a> --}}

                </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p>{{str()->of($profile->about)->limit(150)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($profile->package_id == 20 || $profile->package_id == 24)
        <div class="listing-li pb-3 featured thumbs-2 thumbs-mini">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}</a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper featured">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
               @php
                $coverImage = smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->coverimg->image);
               @endphp

               @else 
               @php
                $coverImage = '';
               @endphp

               @endif
              
                    <img alt="" class="img-responsive" height="135" src="{{$coverImage}}" width="115">
                    
                  </div>
                </span>
              </a>
            </div>
            <div class="other-thumbs pull-left">
          @forelse($profile->multipleimgss->take(2) as $k => $imgs)
                   
              <div class="thumb thumb-0">
                <a class="img img-responsive pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                  <span class="img-wrapper mini">
                    @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                    <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                      <i class="fa fa-check"></i>
                      <span>Verified photos</span>
                    </span>
                    @endif
                    <div class="image-wrapper">
                      <img alt="" class="img-responsive" height="60" src="{{smart_asset("userimages/".$imgs->user_id."/".$imgs->profile_id."/".$imgs->image)}}" width="60">
                    </div>
                  </span>
                </a>
              </div>
        
            @empty
            @endforelse
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}} </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <p>{{str()->of($profile->about)->limit(150)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>

        @elseif($profile->package_id == 19 or empty($profile->package_id))
        <div class="listing-li pb-3 basic thumbs-0 thumbs-basic" style="padding-left:0px">
          <h2 class="visible-xxs">
            <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}  <span class="badge" data-placement="top" data-toggle="tooltip" title="" data-original-title="Curve Amber has answered 3 questions">
                <i class="fa fa-question-circle"></i>
                <span>3</span>
              </span>
            </a>
          </h2>
          <div class="thumbs">
            <div class="main-thumbs">
              <a class="img pb-photo-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">
                <span class="img-wrapper basic">
                  @if(!empty($profile->photoverify) and $profile->photoverify->status == 'approved')
                  <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                    <i class="fa fa-check"></i>
                    <span>Verified photos</span>
                  </span>
                  @endif
                  <div class="image-wrapper">
               @if(!empty($profile->coverimg->image))
                    <img alt="" class="img-responsive" height="95" src="{{smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->coverimg->image)}}" width="89">
             @else
             @if(!empty($profile->singleimg->image))
             <img alt="" class="img-responsive" height="95" src="{{smart_asset("userimages/".$profile->user_id."/".$profile->id."/".$profile->singleimg->image)}}" width="89">
              @endif
                    @endif
                  </div>
                </span>
              </a>
            </div>
          </div>
          <div class="listing-info-wrapper">
            <div class="listing-info">
              <h2>
                <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">{{$profile->name}}  <span class="badge" data-placement="top" data-toggle="tooltip" title="" data-original-title="Curve Amber has answered 3 questions">
                    <i class="fa fa-question-circle"></i>
                    <span>3</span>
                  </span>
                </a>
              </h2>
              <a class="nostyle-link" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}r">
                <p>{{str()->of($profile->about)->limit(150)}}</p>
              </a>
              <p class="no-margin see-more">
                <a class="btn btn-dark" href="{{url($gender.'-escorts-in-'.$cityname.'/'.$profile->id.'/'.$profile->slug)}}">See more &amp; contact</a>
              </p>
            </div>
          </div>
        </div>
        @else

        @endif

        @empty
        <div class="col-md-6"><h2>No Escorts in {{$selectedcity}} yet</h2><p>Register today and we will send you updates with new listings in {{$selectedcity}}</p><p></p><div class="subscribe-btn-wrapper"><a class="btn btn-primary btn-lg btn-lg" data-btn-link="" href="/register"><i class="fa fa-newspaper"></i> Subscribe</a></div><p></p></div>
        @endforelse
        
        
    
      </div>

      <div class="direct-navigation" style="text-align: center; margin: 20px 0; padding: 15px; background: #333; border-radius: 8px; display: flex; justify-content: center; align-items: center; flex-wrap: nowrap;">
        
        
        <div style="display: flex; align-items: center; flex-wrap: nowrap;">
            @if($currentPage > 1)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage - 1]) }}"
                   class="btn btn-primary" style="margin: 0 10px; white-space: nowrap;">
                    &laquo; Previous Page
                </a>
            @else
                <button class="btn btn-secondary" disabled style="margin: 0 10px; white-space: nowrap;">
                    &laquo; Previous Page
                </button>
            @endif
            
        
            
            @if($currentPage < $totalPages)
                <a href="{{ request()->fullUrlWithQuery(['page' => $currentPage + 1]) }}"
                   class="btn btn-primary" style="margin: 0 10px; white-space: nowrap;">
                    Next Page &raquo;
                </a>
            @else
                <button class="btn btn-secondary" disabled style="margin: 0 10px; white-space: nowrap;">
                    Next Page &raquo;
                </button>
            @endif
        </div>
    </div>


    </div>
    <div class="col-md-3 hidden-sm hidden-xs">
      <div class="stream-sidebar">
        <div class="subscribe-btn-wrapper subscribe-btn-wrapper--small-right">
          <a class="btn btn-primary btn-lg" data-btn-link="" href="/register">
            <i class="fa fa-newspaper"></i> Subscribe </a>
        </div>
        <h3>
          <a href="/female-escort-news-in-dubai">What&#39;s new?</a>
        </h3>
        @if($reviews->count() > 0)
        <ul class="activity-stream activity-records-mini">

      @foreach($reviews as $rev)
          <li>
            <div class="activity-record new-review mini">
              <div class="activity-row">
                <div class="headline h3">
                  <i class="fa fa-heart2"></i>New review for <a title="Sweet tanned GFE Big boobs - New in Town, Vietnamese escort in Dubai" href="/female-escorts-in-dubai/sweet-tanned-gfe-big-boobs-new-in-town">{{$rev->getuser->name ?? ''}} </a>
                </div>
                <div class="photo">
                  <a class=" pb-photo-link" href="/female-escorts-in-dubai/sweet-tanned-gfe-big-boobs-new-in-town">
                    <span class="img-wrapper mini">
                      <span class="verified-image text-left small" title="Photos Verified by Massage Republic">
                        <i class="fa fa-check"></i>
                        <span>Verified photos</span>
                      </span>
                      <div class="image-wrapper">
                        <img alt="" class="img-responsive" height="60" src="{{smart_asset("userimages/".$rev->user_id."/".$rev->profile_id."/".$rev->getpic->image)}}" width="60" />
                      </div>
                    </span>
                  </a>
                </div>
                <div class="activity-content">
                  <div class="review-description">
                    <p>{{$rev->review}} </p>
                  </div>
                </div>
              </div>
            </div>
          </li>
          @endforeach
        </ul>
        @endif
        <div class="padding-left">
          <a href="/female-escort-news-in-dubai">See more</a>
        </div>
      </div>
    </div>
  </div>

</div>
    
    <!-- Reviews section -->
    @if($reviews->count() > 0)
    <div class="mt-5">
        <h3>Recent Reviews</h3>
        <div class="reviews-container">
            @foreach($reviews as $rev)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex">
                        @if(!empty($rev->getpic))
                        <div class="mr-3">
                            <img alt="" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;" 
                                 src="{{smart_asset("userimages/".$rev->user_id."/".$rev->profile_id."/".$rev->getpic->image)}}" />
                        </div>
                        @endif
                        <div>
                            <h5 class="card-title">Review for {{$rev->getuser->name ?? 'Escort'}}</h5>
                            <p class="card-text">{{$rev->review}}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="text-center mt-3">
            <a href="/{{ $gender }}-escort-news-in-{{ $cityname }}" class="btn btn-outline-light">
                See more reviews
            </a>
        </div>
    </div>
    @endif
</div>

@push('css')
<style>
    /* Prominent pagination styling */
    .pagination-container {
        margin: 30px 0;
        padding: 15px;
        background-color: #444;
        border-radius: 8px;
    }
    
    .pagination {
        display: flex;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination .page-item {
        margin: 0 5px;
    }
    
    .pagination .page-link {
        display: block;
        padding: 10px 15px;
        background-color: #333;
        border: 2px solid #555;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        font-size: 16px;
        font-weight: bold;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #0056b3;
        color: #fff;
    }
    
    .pagination .page-item.disabled .page-link {
        background-color: #222;
        border-color: #333;
        color: #666;
        cursor: not-allowed;
    }
    
    .pagination .page-link:hover:not([aria-disabled="true"]) {
        background-color: #555;
        border-color: #777;
    }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // Initialize Select2 with dark theme options
        $('select').select2({
            theme: 'classic',
            dropdownCssClass: 'select2-dark-theme'
        });
    });
</script>
@endpush