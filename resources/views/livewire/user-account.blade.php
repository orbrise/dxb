@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid"> 
        <a class="back-link" href="{{url('/')}}" wire:navigate>
            <i class="fa fa-angle-left fa-fw"></i><span class="hidden-xs">Back</span></a>
        <div class="title">
            <h1> <a href="/my-account">My Account</a></h1>
        </div>
    </div>
</div>
@endsection

<div class="container-fluid">
    <div class="content-wrapper no-sidebar">
        <div id="content">
            @include('components.account-nav')
        <div class="mb-3" id="my-account">
          
          @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          
          <div class="row">
            <div class="account-user col-md-12 d-flex flex-wrap gap-3 flex-column flex-sm-row">
              <div class="block m-0 d-flex gap-3 position-relative" style="max-width: 100%; overflow: hidden; overflow-wrap: anywhere">
                <div class="user-img">
                  <img alt="orbrise2&#39;s avatar" class="img-rounded" src="https://www.gravatar.com/avatar/28c5175931d8449e6acb2b72d8900455?s=128&amp;d=identicon" />
                </div>
                <a class="d-flex align-items-center justify-content-center btn-edit p-3 position-absolute" href="/my-account/edit">
                  <i class="fa fa-pencil-alt fa-inline mr-0"></i>
                </a>
                <div>
                  <h2 class="no-margin">
                    <a href="#">{{ auth()->user()->name}}</a>
                  </h2>
                  <ul class="my-account-info list-unstyled">
                    <li>
                      <strong>Account type</strong> @if( auth()->user()->type == 1) Standard @elseif( auth()->user()->type == 2) Individual @elseif( auth()->user()->type == 3) Agency @endif
                    </li>
                    <li>
                      <strong>Email</strong> {{ auth()->user()->email}}
                    </li>
                  </ul>
                </div>
              </div>
              <div class="block m-0 d-flex flex-column justify-content-between gap-3 p-0" style="flex-basis:300px">
                <h2 class="m-0 pt-3 px-3">
                  <strong>Credits ${{auth()->user()->wallet->balance ?? 0}}</strong>
                </h2>
                <div class="d-flex justify-content-between align-items-center gap-3 px-3 mb-3">
                  <a class="btn btn-primary pr-4" href="/purchase-credits" onclick="window.location.href='/purchase-credits'; return false;">
                    <i class="fa fa-coins px-2"></i>
                    <span>Buy more</span>
                  </a>
                </div>
                <div class="block__footer px-3 pb-3">
                  <p class="small mb-0 py-2">We accept:</p>
                  <div class="accepted-payments d-flex align-items-center gap-3 flex-wrap">
                    <img alt="Mastercard logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" width="36" />
                    <img alt="Visa logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" width="36" />
                    <img alt="Bitcoin icon" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc-b522654df0046f6af0e8ac9f67078a87d26069445a01866c1c337bde91bbcd5f.svg" width="24" />
                    <img alt="Lightning network icon" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/btc_lightning-fa0277781a99cded862007ac4d2e5f5fa8fbafec4d9c2b59dbd3b2fc354e91a0.svg" width="42" />
                    <img alt="Neosurf voucher logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/layout/neosurf-bd53910fca644afad7f8660597f25b84b5c97418652d1c2794ab5f1462e2faf7.svg" width="50" />
                    <img alt="Payprocc Payments Promptpay logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_promptpay_logo-6b70d10f79bdff9dc1efde97f390f89f91019db1b14c81389d20be94fd270275.svg" style="filter: brightness(2)" width="72" />
                    <img alt="Payprocc Payments Momo logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_momo_logo-41d3eadebd629ec10dd28d7c13683b8c5cf564e37eae34ae478de6d89a72b26c.svg" width="24" />
                    <img alt="Payprocc Payments Viettelpay logo" height="24" src="https://d257pz9kz95xf4.cloudfront.net/assets/pay_viettelpay_logo-f99d7823ad8c66b5da8a2248c1877215a648cb16703f63c641584d684f73019b.svg" width="26" />
                  </div>
                </div>
              </div>
              @php
                  // Get unread message count for communication box
                  $userProfiles = \App\Models\UsersProfile::where('user_id', auth()->id())->pluck('id');
                  $unreadMsgCount = \App\Models\Message::whereIn('profile_id', $userProfiles)
                      ->where(function($q) {
                          $q->whereNull('status')->orWhere('status', 'unread');
                      })
                      ->count();
              @endphp
              <div class="block account-communication m-0" style="flex-basis: 200px;">
                <h2 class="h4">
                  <i class="fa fa-comments" style="color: #f4b942;"></i> Communication
                  @if($unreadMsgCount > 0)
                    <span class="badge" style="background-color: #dc3545; font-size: 11px; padding: 4px 8px; border-radius: 10px; margin-left: 5px;">{{ $unreadMsgCount }}</span>
                  @endif
                </h2>
                <ul class="list-unstyled" style="margin: 0; padding: 0;">
                  <li style="margin-bottom: 8px;">
                    <a href="{{ route('user.chat') }}" style="color: #f4b942; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                      <i class="fa fa-envelope" style="width: 16px;"></i> Messages
                      @if($unreadMsgCount > 0)<span class="badge" style="background-color: #dc3545; font-size: 10px; padding: 2px 6px; border-radius: 8px;">{{ $unreadMsgCount }}</span>@endif
                    </a>
                  </li>
                  <li style="margin-bottom: 8px;">
                    <a href="{{ route('user.questions') }}" style="color: #f4b942; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                      <i class="fa fa-question-circle" style="width: 16px;"></i> Questions
                    </a>
                  </li>
                  <li style="margin-bottom: 8px;">
                    <a href="{{ route('user.reviews') }}" style="color: #f4b942; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                      <i class="fa fa-star" style="width: 16px;"></i> Reviews
                    </a>
                  </li>
                  <li>
                    <a href="{{ route('favorites.dashboard') }}" style="color: #f4b942; text-decoration: none; display: flex; align-items: center; gap: 8px;">
                      <i class="fa fa-heart" style="width: 16px;"></i> My Favorites
                    </a>
                  </li>
                </ul>
              </div>
              <div class="block account-newsletter m-0" x-data="{ show: false }">
                <h2 class="h4">
                  <i class="fa fa-newspaper"></i> Newsletter
                </h2>
                <p>Receiving monthly updates in {{ auth()->user()->newsletterSubscriptions()->count() }} cities. <br />
                  <a href="#" @click.prevent="show = true; $wire.call('loadUserSettings')">
                    <i class="fa fa-pencil-alt fa-inline"></i>Edit Subscription </a>
                </p>
                
                <!-- Newsletter Modal with Alpine.js -->
                <div x-show="show" 
                     x-cloak
                     style="background: rgba(0,0,0,0.85); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 99999;" 
                     @click.self="show = false">
                    <div style="position: relative; width: auto; margin: 3rem auto; max-width: 500px;">
                        <div style="background: #4a4a4a; color: #fff; position: relative; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.5);">
                            <!-- Header -->
                            <div style="padding: 1.5rem; border-bottom: 1px solid #5a5a5a;">
                                <h2 style="margin: 0; color: #fff; font-size: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                                    <i class="fa fa-newspaper" style="color: #f4b942;"></i>
                                    <span>Newsletter</span>
                                </h2>
                                <button type="button" style="position: absolute; right: 1.25rem; top: 1.25rem; background: #6a6a6a; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; width: 32px; height: 32px; border-radius: 4px; display: flex; align-items: center; justify-content: center; transition: background 0.2s;" @click="show = false" onmouseover="this.style.background='#7a7a7a'" onmouseout="this.style.background='#6a6a6a'">
                                    <span style="line-height: 1;">&times;</span>
                                </button>
                            </div>
                            
                            <!-- Body -->
                            <div style="padding: 1.5rem;">
                                <!-- Checkbox -->
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: flex; align-items: center; cursor: pointer; font-size: 1rem;">
                                        <input type="checkbox" id="receiveNewsletter" wire:model.live="receiveNewsletter" style="width: 18px; height: 18px; margin: 0; cursor: pointer;">
                                        <span style="margin-left: 0.75rem; font-weight: 500;">Send me newsletter for:</span>
                                    </label>
                                </div>

                                <!-- City Search -->
                                <div style="margin-bottom: 1rem; position: relative;">
                                    <div style="display: flex; border: 2px solid #5a5a5a; border-radius: 6px; overflow: hidden; background: #3a3a3a;">
                                        <span style="padding: 0.75rem 1rem; background: #3a3a3a; display: flex; align-items: center;">
                                            <i class="fa fa-map-marker-alt" style="color: #f4b942;"></i>
                                        </span>
                                        <input 
                                            type="text" 
                                            style="flex: 1; padding: 0.75rem; background: transparent; border: none; color: #fff; outline: none; font-size: 0.95rem;" 
                                            placeholder="Find city..."
                                            wire:model.live="citySearch"
                                            autocomplete="off"
                                        >
                                        @if($citySearch)
                                            <button type="button" style="padding: 0.75rem 1rem; background: transparent; border: none; color: #f4b942; cursor: pointer; display: flex; align-items: center;" wire:click="$set('citySearch', '')">
                                                <i class="fa fa-times" style="font-size: 1.2rem;"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    @if(count($searchResults) > 0)
                                        <div style="position: absolute; width: 100%; z-index: 1000; max-height: 200px; overflow-y: auto; background: #3a3a3a; border: 2px solid #5a5a5a; border-radius: 6px; margin-top: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
                                            @foreach($searchResults as $city)
                                                <button 
                                                    type="button"
                                                    style="display: block; width: 100%; padding: 0.75rem 1rem; background: #3a3a3a; color: #fff; border: none; border-bottom: 1px solid #5a5a5a; text-align: left; cursor: pointer; transition: background 0.2s;"
                                                    wire:click="addCity({{ $city['id'] }})"
                                                    onmouseover="this.style.background='#4a4a4a'"
                                                    onmouseout="this.style.background='#3a3a3a'"
                                                >
                                                    {{ $city['name'] }}@if($city['country']) <span style="color: #aaa;">({{ $city['country'] }})</span>@endif
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Selected Cities -->
                                @if(count($selectedCities) > 0)
                                    <div style="margin-bottom: 1rem; max-height: 150px; overflow-y: auto;">
                                        @foreach($selectedCities as $index => $city)
                                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; padding: 0.75rem 1rem; background: #3a3a3a; border-radius: 6px; border: 1px solid #5a5a5a;">
                                                <span style="color: #fff;">
                                                    <i class="fa fa-map-marker-alt" style="color: #f4b942; margin-right: 0.5rem;"></i>
                                                    {{ $city['name'] }}@if($city['country']) <span style="color: #aaa;">({{ $city['country'] }})</span>@endif
                                                </span>
                                                <button 
                                                    type="button" 
                                                    style="background: none; border: none; color: #f4b942; cursor: pointer; font-size: 1.2rem; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; transition: transform 0.2s;"
                                                    wire:click="removeCity({{ $index }})"
                                                    onmouseover="this.style.transform='scale(1.2)'"
                                                    onmouseout="this.style.transform='scale(1)'"
                                                >
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Add City Button -->
                                <button type="button" style="padding: 0.75rem 1.25rem; margin-bottom: 1.5rem; background: #3a3a3a; color: #f4b942; border: 2px solid #5a5a5a; border-radius: 6px; cursor: pointer; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem;" wire:click="$set('citySearch', '')" onmouseover="this.style.background='#4a4a4a'; this.style.borderColor='#6a6a6a'" onmouseout="this.style.background='#3a3a3a'; this.style.borderColor='#5a5a5a'">
                                    <i class="fa fa-plus"></i>
                                    <span>Add city</span>
                                </button>

                                <!-- Include Genders -->
                                <div>
                                    <label style="display: block; margin-bottom: 1rem; font-weight: 600; font-size: 1rem;">Include</label>
                                    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem;">
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" id="gender_female" value="female" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer;">
                                            <span style="margin-left: 0.5rem;">Escorts</span>
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" id="gender_male" value="male" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer;">
                                            <span style="margin-left: 0.5rem;">Male Escorts</span>
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" id="gender_shemale" value="shemale" wire:model="selectedGenders" style="width: 18px; height: 18px; margin: 0; cursor: pointer;">
                                            <span style="margin-left: 0.5rem;">Shemale Escorts</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div style="padding: 1.5rem; border-top: 1px solid #5a5a5a; text-align: right;">
                                <button type="button" style="padding: 0.75rem 2rem; background: #f4b942; border: none; color: #000; border-radius: 6px; cursor: pointer; font-size: 1rem; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 0.5rem;" wire:click="saveNewsletter" @click="show = false" onmouseover="this.style.background='#f5c962'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(244,185,66,0.4)'" onmouseout="this.style.background='#f4b942'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                    <span>Save</span>
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
          <form action="{{ route('user.account.delete') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to DELETE your account? This will deactivate your account and all your profiles. This action cannot be reversed.')">
            @csrf
            <button type="submit" class="pull-right py-3 text-muted small btn-link" style="background: none; border: none; padding: 0; cursor: pointer; text-decoration: none;">
              <i class="fa fa-times fa-inline"></i>delete account
            </button>
          </form>
        </div>
      </div>
    </div>

  </div>