@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="{{route('new.profile')}}">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">Back to Form</span>
      </a>
      <div class="title">
        <h1>
          <a href="">Select Your Package</a>
        </h1>
      </div>
    </div>
  </div>
@endsection

<style>
.upgrade-listing-form-init { visibility: visible; }
#allpackages{display: block;}
.checkout-fields{ display: none; }
#paypal-button-container { margin-top: 20px; width: 100%; }
.payment-options li label.selected { border-left: 3px solid gold; }


/* Payment Method Options - Modern Card Style */
.payment-method-option {
    padding: 20px;
    border: 2px solid #3d3d3d;
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s;
    background: #2d2d2d;
}

.payment-method-option:hover {
    border-color: #555;
}

.payment-method-option.selected {
    border-color: #ffc439 !important;
    background-color: #1a1a1a !important;
}

.payment-method-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.payment-method-option.disabled:hover {
    border-color: #3d3d3d;
}

.payment-method-option input[type="radio"] {
    accent-color: #ffc439;
    width: 18px;
    height: 18px;
    margin-right: 15px;
}

.payment-method-option label {
    color: #ffc439 !important;
    cursor: pointer;
    margin-bottom: 0;
}

.payment-method-option .account-balance-amount {
    margin-left: auto;
    color: #28a745;
    font-weight: bold;
}

.checkout-button { position: relative !important; z-index: 100 !important; pointer-events: auto !important; }
.upgrade-type { position: relative; min-height: 330px; cursor: pointer; }

/* Selected Package Styling */
.upgrade-type.active {
    border: 3px solid #c9910a !important;
    box-shadow: 0 0 15px rgba(201, 145, 10, -0.5) !important;
    position: relative;
}

/* Hide choose button and show selected badge when active */
.upgrade-type.active .choose-package-btn {
    display: none !important;
}

.upgrade-type.active .selected-badge {
    display: inline-block !important;
}

/* Package tagline styling */
.package-tagline {
    font-weight: 500;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.selected-badge {
    margin: 0;
    display: none;
    background-color: #5cb85c;
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 13px;
    z-index: 10;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.selected-badge i {
    margin-right: 5px;
}

/* Mobile: Fixed button at bottom of screen */
@media (max-width: 767px) {
    .upgrade-type.active .selected-badge {
        display: block !important;
    }
    .upgrade-button-wrapper.mobile-fixed,
    .free-button-wrapper.mobile-fixed {
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        margin: 0 !important;
        padding: 15px !important;
        background: black !important;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1) !important;
        z-index: 99999 !important;
        width: 100% !important;
        border-top: 1px solid white;
    }
    .upgrade-button-wrapper.mobile-fixed .checkout-button,
    .free-button-wrapper.mobile-fixed .free-profile-button {
        width: 100% !important;
        margin: 0 !important;
        position: static !important;
    }
    /* Add padding to body so content doesn't hide behind fixed button */
    body.has-fixed-button {
        padding-bottom: 90px !important;
    }
}

@media (max-width: 768px) {
    .upgrade-type {
    margin: 7px 4px;
    background: rgb(0 0 0 / 54%);
    }

    .package-text-content {
        height: auto !important;
    }

    .upgrade-modal .upgrade-type.active, .upgrade-type.active:hover {
    transform: matrix(1, 0, 0, 1, 0, 0);

    -webkit-transition: auto;
    transition: auto;

}


}

.payment-method-option input[type="radio"] {
    margin-top: -1px;
}

a.text-warning:focus, a.text-warning
 {
    color: #f4b827;
}

.btn-warning {
    background: #f4b827;
}
.btn-warning:focus, .btn-warning:hover {
    background-color: #f4b827;
   color: #333;
}

.btn-warning.active, .btn-warning:active, .btn-warning:hover, .open>.btn-warning.dropdown-toggle {
    color: #333;
    background-color: #f4b827;
    border-color: #f4b827;
}
.btn-warning.active.focus, .btn-warning.active:focus, .btn-warning.active:hover, .btn-warning:active.focus, .btn-warning:active:focus, .btn-warning:active:hover, .open>.btn-warning.dropdown-toggle.focus, .open>.btn-warning.dropdown-toggle:focus, .open>.btn-warning.dropdown-toggle:hover

 {
    color: #333;
    background-color: #f4b827;
    border-color: #f4b827;
}

.form-group {
    margin-bottom: 10px;
}
</style>

<div>
  <div class="container-fluid">
        <div class="content-wrapper no-sidebar">
          <div id="content">
            
            <form class="simple_form upgrade-listing-form upgrade-listing-form-init  free-visible" id="new_upgrade_process">
              <div class="upgrade-type-selector">
                <div class="row">
                  <div class="col-lg-offset-1 col-lg-10">
                    <p class="text-center alert alert-info">Please select a package for your new profile or click Free to create a free profile.</p>
                    <div class="row" id="allpackages">
                      <div class="col-sm-3">
                        <div class="free upgrade-type upgrade-type-free current" style="cursor: pointer;" data-package="free">
                          <div class="form-group radio_buttons optional upgrade_process_upgrade_type">
                            <span class="radio-inline">
                              <label><input class="radio_buttons optional" type="radio" value="free" name="upgrade_process[upgrade_type]" />Free </label>
                            </span>
                          </div>
                          <div class='until'>Current</div>
                        </div>
                      </div>
                   
                      @foreach($packages as $package)
                      <div class="col-sm-3">
                          <div class="{{strtolower($package->name)}}  upgrade-type upgrade-type-{{strtolower($package->name)}}" data-package="{{$package->id}}">
                              <div class="form-group radio_buttons optional upgrade_process_upgrade_type">
                                  <span class="radio-inline">
                                      <label for="upgrade_process_upgrade_type_{{strtolower($package->name)}}">
                                          <input class="radio_buttons optional" type="radio" value="{{strtolower($package->name)}}" name="upgrade_process[upgrade_type]" id="upgrade_process_upgrade_type_{{strtolower($package->name)}}" />
                                          {{$package->name}}
                                      </label>
                                  </span>
                              </div>
                              
                              <!-- Profile Preview -->
                              @php
                                  // Set image size based on package
                                  $imageSize = '80px'; // default
                                  $minHeight = '200px';
                                  $textLimit = 120;
                                  $showSideImages = 0; // Number of side images to show
                                  
                                  if($package->name == 'Basic') {
                                      $imageSize = '45px';
                                      $minHeight = '45px';
                                      $textLimit = 190;
                                      $showSideImages = 0;
                                  } elseif($package->name == 'Featured') {
                                      $imageSize = '60px';
                                      $minHeight = '60px';
                                      $textLimit = 80;
                                      $showSideImages = 2;
                                  } elseif($package->name == 'VIP') {
                                      $imageSize = '90px';
                                      $minHeight = '90px';
                                      $textLimit =  90;
                                      $showSideImages = 3;
                                  }
                              @endphp
                              
                              <div class="profile-preview" style="background: #000; padding: 10px; margin: 10px 0; min-height: {{ $minHeight }}; display: flex; align-items: flex-start; gap: 10px;">
                                  <!-- Main Image -->
                                  <div style="width: {{ $imageSize }}; height: {{ $imageSize }}; background: #fff; flex-shrink: 0; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                      @if($profileImage && file_exists($profileImage))
                                          <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents($profileImage)) }}" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                      @else
                                          <img src="/admin/assets1/images/default-avatar.png" alt="Profile Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                      @endif
                                  </div>
                                  
                                  @if($showSideImages > 0)
                                  <!-- Side Images Column (placeholder for new profiles) -->
                                  <div style="display: flex; flex-direction: column; gap: 3px; flex-shrink: 0;">
                                      @php
                                          $sideImageSize = $package->name == 'VIP' ? '25px' : '25px';
                                      @endphp
                                      @for($i = 0; $i < $showSideImages; $i++)
                                      <div style="width: {{ $sideImageSize }}; height: {{ $sideImageSize }}; background: #333; overflow: hidden;">
                                          <img src="/admin/assets1/images/default-avatar.png" alt="Side Image Slot" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.5;">
                                      </div>
                                      @endfor
                                  </div>
                                  @endif
                                  
                                  <div class="package-text-content" style="color: #fff; font-size: 10px; overflow: hidden; text-align: left; flex: 1;height:172px;">
                                      <strong style="display: block; margin-bottom: 5px;">{{ $profileName }}</strong>
                                      <p style="margin: 0; line-height: 1.4; word-wrap: break-word; max-height: 113px; overflow: hidden;">{{ Str::limit($profileAbout, $textLimit) }}</p>
                                  </div>
                              </div>
                              
                              <div class="u-price" style="text-align: center; margin-top: 0px;">
                                  <!-- Tagline -->
                                  <div class="package-tagline" style="font-size: 1em; color: white; ">
                                      {{ $package->tagline ?? 'Best value package' }}
                                  </div>
                                  
                                  @php
                                      // Check if it's a global package first
                                      if ($package->is_global && $package->price_tiers) {
                                          $tiers = is_string($package->price_tiers) ? json_decode($package->price_tiers, true) : $package->price_tiers;
                                          $minPrice = !empty($tiers) ? min(array_column($tiers, 'price')) : 0;
                                      } else {
                                          // Country-specific package
                                          $countryPrice = $package->countryPrices->first();
                                          if ($countryPrice && $countryPrice->price_tiers) {
                                              $tiers = json_decode($countryPrice->price_tiers, true);
                                              $minPrice = !empty($tiers) ? min(array_column($tiers, 'price')) : 0;
                                          } else {
                                              $minPrice = 0;
                                          }
                                      }
                                  @endphp
                                  
                                  <!-- Price -->
                                  <strong style="display: block; font-size: 18px; margin-bottom:  5px;">from  ${{$minPrice}}</strong>
                                  
                                  <!-- Choose Button -->
                                  <button type="button" class="btn btn-warning btn-sm choose-package-btn" style="padding: 8px 25px; border-radius:  4px; font-weight: bold;">
                                      Choose <i class="fa fa-arrow-right"></i>
                                  </button>
                                  
                                  <span class="selected-badge" style="display: none; margin-top: 10px;">
                                      <i class="fa fa-check"></i> Selected
                                  </span>
                              </div>
                          </div>
                      </div>
                      @endforeach
                      
                    </div>
                    <div class="clearfix"></div>
                    
                    <!-- Upgrade Button - Shows after paid package selection -->
                    <div class='upgrade-button-wrapper text-center' style='display: none; margin: 30px 0;'>
                        <button class='btn btn-primary btn-lg checkout-button' type='button' style='padding: 15px 40px; font-size: 18px;'>
                            Proceed Upgrade <i class='fa fa-arrow-right'></i>
                        </button>
                    </div>
                    
                    <!-- Free Profile Button - Shows after free package selection -->
                    <div class='free-button-wrapper text-center' style='display: none; margin: 30px 0;'>
                        <button class='btn btn-success btn-lg free-profile-button' type='button' style='padding: 15px 40px; font-size: 18px;' onclick="@this.call('skipPackage')">
                            Add Free Profile <i class='fa fa-check'></i>
                        </button>
                    </div>
                    
                    <!-- Checkout Section - Appears Between Packages and Bottom Text -->
                    <div class="checkout-fields">
                      <div class="row">
                        <div class="col-sm-9 col-md-7 col-md-offset-1 col-lg-6 col-lg-offset-3">
                        <div class="block pb-0 mb-3 upgrade-duration">
                          <div class="upgrade-title d-flex justify-content-between align-items-center mt-0 mb-2">
                            <h2 class="upgrade-name font-weight-bold my-0 mr-2" data-listing-upgrade-form-selected-upgrade-type-display=""></h2>
                            <a class="btn btn-primary d-flex align-items-center justify-content-center" data-listing-upgrade-form-back-to-upgrade-selection-btn="" href="#">
                              <i class="fa fa-arrow-left"></i>
                              <span class="ml-2">Back to selection</span>
                            </a>
                          </div>
                          <div class="border-top padding-top">
                            <h4 class="mb-3" style="font-weight: 600;">Select duration</h4>
                            <div class="upgrade-duration__radios mb-3"></div>
                            <div class="text-right w-100 pb-3">
                              <strong class="lead">Total: $ <span id="famount"></span></strong>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Account Balance Section -->
                        <div class="block px-4 py-3 mb-3 block--payment" id="account-balance-section" style="display: none;">
                          <div class="credits-label p-2">
                            <h2 class="inline-block payment-options-box__title">Account Balance</h2>
                          </div>
                          <ul class="payment-options toggle-li-radio list-unstyled mb-0">
                            <li class="mb-1">
                              <label class="p-3 d-flex flex-wrap align-items-center mb-0 payment-method-option {{ auth()->user()->wallet && auth()->user()->wallet->balance > 0 ? '' : 'disabled' }}" for="payment_method_wallet" data-payment-method="wallet" style="min-height:80px">
                                <input class="payment-option-input" type="radio" value="wallet" name="payment_method" id="payment_method_wallet" {{ auth()->user()->wallet && auth()->user()->wallet->balance > 0 ? '' : 'disabled' }}>
                                <span>Available balance</span>
                                <span class="account-balance" style="margin-left: 10px; color: white; font-weight: bold;">${{ auth()->user()->wallet ? number_format(auth()->user()->wallet->balance, 0) : 0 }}</span>
                                <span style="margin-left: 10px;">
                                  <a href="{{ route('purchase.credits') }}#wallet" class="text-warning" style="font-size:15px">Purchase account balance</a>
                                </span>
                              </label>
                            </li>
                          </ul>
                        </div>


                        <!-- Payment Method Selection -->
                        <div class="block px-0 mb-3" id="payment-methods-section" style="display: none;">
                          <div class="p-3" >
                            <h2 class="payment-options-box__title d-flex align-items-center mb-0"><span style="margin-right:10px">Credit/Debit Card</span> <i class="fa fa-credit-card mr-2 " style="margin-left:10px"></i> </h2>
                          </div>
                          <div class="p-3">
                            <!-- Primary Gateway Option -->
                            <div class="payment-method-option d-flex align-items-center disabled" data-payment-method="primary">
                              <input type="radio" name="payment_method" id="payment_method_primary" value="primary" disabled>
                              <label for="payment_method_primary" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                                <span style="font-size: 20px;margin-right: 10px;">Primary Gateway</span>
                                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 50px; margin-right: 8px;">
                                <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 60px;">
                              </label>
                            </div>
                            
                            <!-- Secondary Gateway (PayPal) Option -->
                            <div class="payment-method-option d-flex align-items-center disabled mt-2" data-payment-method="paypal">
                              <input type="radio" name="payment_method" id="payment_method_paypal" value="paypal" disabled>
                              <label for="payment_method_paypal" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                                <span style="font-size: 20px;margin-right: 10px;">Secondary Gateway</span>
                               <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 50px; margin-right: 8px;">
                                <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 60px;">
                              </label>
                            </div>
                          </div>
                        </div>
                        
                        <!-- Wallet Payment Section -->
                        <div id="wallet-payment-section" class="block p-0 mb-3 payment-section" style="display: none;">
                          <div class="p-3">
                            <p>You will be charged <strong>$<span class="payment-amount"></span></strong> from your account Credits.</p>
                            <button class="d-flex align-items-center justify-content-center mt-4 btn btn-block btn-primary btn-lg btn-xs-block" type="button" id="wallet-payment-button">
                              <span class="button-text">Pay $<span class="payment-amount"></span> from Credits</span>
                              <span class="button-spinner" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i> Processing...
                              </span>
                            </button>
                          </div>
                        </div>
                        
                        <!-- Primary Gateway Payment Section -->
                        <div id="primary-payment-section" class="block p-0 mb-3 payment-section" style="display: none;">
                          <div class="p-3">
                            <p>You will be charged <strong>$<span class="payment-amount"></span></strong> via secure payment gateway.</p>
                            
                            <!-- Iframe Container -->
                            <div id="primary-gateway-container" class="mt-3">
                              <div id="primary-gateway-loading" style="text-align: center; padding: 30px; color: #fff;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <p class="mt-2">Loading secure payment form...</p>
                              </div>
                              <iframe id="primary-gateway-iframe" style="display: none; width: 100%; min-height: 450px; border: none; border-radius: 8px;" allowpaymentrequest></iframe>
                            </div>
                            
                            <p class="small text-muted text-center mt-3">Secure payment processing. Your card details are protected.</p>
                          </div>
                        </div>
                        
                        <!-- PayPal Payment Section -->
                        <div id="paypal-payment-section" class="block p-0 mb-3 payment-section" style="display: none;background: #f0f0f0;">
                          <div class="p-3" style="color:#4e4e4e">
                            <p>You will be charged <strong>$<span class="payment-amount"></span></strong> via PayPal.</p>
                            <div id="paypal-button-container" class="mt-3"></div>
                            <p class="small text-muted text-center mt-3" style="color:#4e4e4e">Secure payment processing by PayPal. You can use your PayPal account or credit/debit card.</p>
                          </div>
                        </div>
                        
                      </div>
                    </div>
                  </div>
                  
                    <div class="upgrade-types-bottom-text">
                      <p class="text-muted lead">To find out more about the different upgrade options <a href="/help-for-advertisers#upgrade-options" target="_blank">click here</a>. </p>
                    </div>
                  </div>
                </div>
              </div>
              
      </form>
    </div>
  </div>
</div>
</div>

@push('js')
<script>
(function() {
    // Prevent multiple initializations
    if (window.newProfileUpgradeInitialized) return;
    window.newProfileUpgradeInitialized = true;
    
    var selectedPackageId = null;
    var selectedDuration = null;
    var selectedPrice = null;
    
    // Get Livewire component ID from the page
    function getLivewireComponent() {
        var el = document.querySelector('[wire\\:id]');
        if (el) {
            var wireId = el.getAttribute('wire:id');
            if (wireId && typeof Livewire !== 'undefined') {
                var component = Livewire.find(wireId);
                console.log('Found Livewire component:', component);
                return component;
            }
        }
        return null;
    }
    
    // Set Livewire property
    function setLivewireProperty(name, value) {
        var component = getLivewireComponent();
        if (component) {
            if (typeof component.set === 'function') {
                component.set(name, value);
            } else if (typeof component.$set === 'function') {
                component.$set(name, value);
            } else {
                component[name] = value;
            }
        }
    }
    
    function initNewProfileUpgradePage() {
        console.log('=== initNewProfileUpgradePage CALLED ===');
        
        if (typeof jQuery === 'undefined') {
            setTimeout(initNewProfileUpgradePage, 50);
            return;
        }
        
        // Package card clicks
        $(document).off('click.newupgrade', '.upgrade-type').on('click.newupgrade', '.upgrade-type', function(e) {
            console.log('Package card clicked');
            
            if ($(e.target).closest('.upgrade-button-wrapper, .free-button-wrapper').length > 0) return;
            e.preventDefault();
            
            $('.upgrade-type').removeClass('active');
            $('.upgrade-button-wrapper, .free-button-wrapper, .checkout-fields').hide();
            $(this).addClass('active');
            
            // Handle free package
            if ($(this).hasClass('upgrade-type-free')) {
                selectedPackageId = 'free';
                if ($(window).width() <= 767) {
                    var $wrapper = $('.free-button-wrapper');
                    if (!$wrapper.hasClass('mobile-fixed')) {
                        $wrapper.addClass('mobile-fixed').appendTo('body');
                        $('body').addClass('has-fixed-button');
                    }
                }
                $('.free-button-wrapper').show();
                console.log('Free package selected');
            } else {
                selectedPackageId = $(this).data('package');
                if ($(window).width() <= 767) {
                    var $wrapper = $('.upgrade-button-wrapper');
                    if (!$wrapper.hasClass('mobile-fixed')) {
                        $wrapper.addClass('mobile-fixed').appendTo('body');
                        $('body').addClass('has-fixed-button');
                    }
                }
                $('.upgrade-button-wrapper').show();
                console.log('Package ID:', selectedPackageId);
            }
        });
        
        // Free profile button
        $(document).off('click.newupgrade', '.free-profile-button').on('click.newupgrade', '.free-profile-button', function(e) {
            e.preventDefault();
            var component = getLivewireComponent();
            if (component) {
                component.call('skipPackage');
            }
        });
        
        // Checkout button
        $(document).off('click.newupgrade', '.checkout-button').on('click.newupgrade', '.checkout-button', function(e) {
            e.preventDefault();
            if (!selectedPackageId || selectedPackageId === 'free') {
                alert('Please select a paid package first');
                return;
            }
            
            $('.upgrade-name').text($('.upgrade-type.active label').first().text().trim());
            $('#allpackages, .upgrade-button-wrapper').hide();
            $('body').removeClass('has-fixed-button');
            $('.checkout-fields').show();
            
            setTimeout(function() {
                $('html, body').animate({ scrollTop: $('.checkout-fields').offset().top - 100 }, 500);
            }, 100);
            loadPackagePricing(selectedPackageId);
        });
        
        // Back button
        $(document).off('click.newupgrade', '[data-listing-upgrade-form-back-to-upgrade-selection-btn]').on('click.newupgrade', '[data-listing-upgrade-form-back-to-upgrade-selection-btn]', function(e) {
            e.preventDefault();
            $('.checkout-fields').hide();
            $('#allpackages').show();
            
            if (selectedPackageId === 'free') {
                $('.free-button-wrapper').show();
                if ($(window).width() <= 767) {
                    var $wrapper = $('.free-button-wrapper');
                    if (!$wrapper.hasClass('mobile-fixed')) {
                        $wrapper.addClass('mobile-fixed').appendTo('body');
                        $('body').addClass('has-fixed-button');
                    }
                }
            } else {
                $('.upgrade-button-wrapper').show();
                if ($(window).width() <= 767) {
                    var $wrapper = $('.upgrade-button-wrapper');
                    if (!$wrapper.hasClass('mobile-fixed')) {
                        $wrapper.addClass('mobile-fixed').appendTo('body');
                        $('body').addClass('has-fixed-button');
                    }
                }
            }
            
            selectedPackageId = selectedDuration = selectedPrice = null;
            $('#account-balance-section, #payment-methods-section, .payment-section').hide();
            $('.payment-method-option').removeClass('selected');
            $('[data-payment-method="primary"], [data-payment-method="paypal"]').addClass('disabled');
            $('#payment_method_wallet, #payment_method_primary, #payment_method_paypal').prop('disabled', true).prop('checked', false);
        });
        
        // Duration change
        $(document).off('change.newupgrade', 'input[name="duration"]').on('change.newupgrade', 'input[name="duration"]', function() {
            selectedPrice = $(this).data('price');
            selectedDuration = $(this).val();
            $('span#famount, .payment-amount').text(selectedPrice);
            
            // Show both payment sections
            $('#account-balance-section, #payment-methods-section').slideDown();
            
            // Enable Primary Gateway option
            $('[data-payment-method="primary"]').removeClass('disabled');
            $('#payment_method_primary').prop('disabled', false);
            
            // Enable PayPal option
            $('[data-payment-method="paypal"]').removeClass('disabled');
            $('#payment_method_paypal').prop('disabled', false);
            
            // Enable wallet option only if balance is sufficient
            var walletBalance = {{ auth()->user()->wallet ? auth()->user()->wallet->balance : 0 }};
            if (walletBalance >= selectedPrice) {
                $('[data-payment-method="wallet"]').removeClass('disabled');
                $('#payment_method_wallet').prop('disabled', false);
            } else {
                $('[data-payment-method="wallet"]').addClass('disabled');
                $('#payment_method_wallet').prop('disabled', true);
            }
        });
        
        // Payment method click
        $(document).off('click.newupgrade', '.payment-method-option').on('click.newupgrade', '.payment-method-option', function(e) {
            if ($(this).hasClass('disabled')) return false;
            var method = $(this).data('payment-method');
            $('.payment-method-option').removeClass('selected');
            $(this).addClass('selected').find('input[type="radio"]').prop('checked', true);
            $('.payment-section').hide();
            $('#' + method + '-payment-section').show();
            if (method === 'paypal') initPayPalButtons();
            // Load primary gateway iframe when switching to it
            if (method === 'primary') {
                initPrimaryGatewayIframe();
                // Scroll to iframe on mobile
                setTimeout(function() {
                    var $target = $('#primary-payment-section');
                    if ($target.length && $(window).width() <= 767) {
                        $('html, body').animate({ scrollTop: $target.offset().top - 20 }, 400);
                    }
                }, 150);
            }
        });
        
        // Wallet payment button
        $(document).off('click.newupgrade', '#wallet-payment-button').on('click.newupgrade', '#wallet-payment-button', function() {
            if (!selectedPackageId || !selectedDuration || !selectedPrice) {
                alert('Please select a package and duration first');
                return;
            }
            var $btn = $(this);
            $btn.prop('disabled', true).find('.button-text').hide();
            $btn.find('.button-spinner').show();
            
            // Pass parameters directly with dispatch
            Livewire.dispatch('processWalletPayment', { 
                packageId: selectedPackageId, 
                duration: parseInt(selectedDuration), 
                amount: parseFloat(selectedPrice) 
            });
        });
         
        // Listen for message from iframe
        window.addEventListener('message', function(event) {
            // Accept messages from external payment site or same origin
            if (event.origin !== 'https://myadsnetwork.com' && event.origin !== window.location.origin) return;
            
            if (event.data && event.data.type === 'payment_success') {
                var ref = event.data.reference_id;
                
                // Prevent duplicate processing
                if (window.primaryPaymentProcessed && window.primaryPaymentProcessed === ref) {
                    console.log('Payment already processed, ignoring duplicate message');
                    return;
                }
                window.primaryPaymentProcessed = ref;
                window.primaryPaymentSuccess = true;
                
                // Show success in iframe container
                $('#primary-gateway-iframe').hide();
                $('#primary-gateway-loading').html('<i class="fa fa-check-circle fa-2x text-success"></i><p class="mt-2">Payment successful! Processing...</p>').show();
                
                // Process the payment in Livewire
                Livewire.dispatch('processPrimaryPayment', { 
                    packageId: window.primaryPaymentReference.packageId, 
                    duration: parseInt(window.primaryPaymentReference.duration), 
                    amount: parseFloat(window.primaryPaymentReference.price),
                    referenceId: ref
                });
            }
        });
    }
    
    function initPrimaryGatewayIframe() {
        console.log('=== initPrimaryGatewayIframe CALLED ===');
        
        var $loading = $('#primary-gateway-loading');
        var $iframe = $('#primary-gateway-iframe');
        
        if (!selectedPackageId || !selectedDuration || !selectedPrice) {
            $('#primary-gateway-container').html('<p class="text-danger">Please select a duration first.</p>');
            return;
        }
        
        // Show loading
        $loading.show();
        $iframe.hide();
        
        // Get package name from selected package card
        var packageName = $('.upgrade-type.active label').first().text().trim() || 'Package';
        
        // Generate a unique reference ID
        var referenceId = 'DXB_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Store reference for callback
        window.primaryPaymentReference = {
            referenceId: referenceId,
            packageId: selectedPackageId,
            duration: selectedDuration,
            price: selectedPrice
        };
        
        // Build the external payment URL
        var callbackUrl = encodeURIComponent(window.location.origin + '/payment/primary-callback?reference_id=' + referenceId);
        var cancelUrl = encodeURIComponent(window.location.href);
        
        var externalPaymentUrl = 'https://myadsnetwork.com/external-payment/checkout' +
            '?price=' + selectedPrice +
            '&name=' + encodeURIComponent(packageName + ' - ' + selectedDuration + ' days') +
            '&package_name=' + encodeURIComponent(packageName) +
            '&package_id=' + selectedPackageId +
            '&duration=' + selectedDuration +
            '&currency=USD' +
            '&callback_url=' + callbackUrl +
            '&reference_id=' + referenceId +
            '&customer_email=' + encodeURIComponent('{{ auth()->user()->email }}') +
            '&embed=1';
        
        console.log('Loading iframe:', externalPaymentUrl);
        
        // Set iframe source
        $iframe.attr('src', externalPaymentUrl);
        
        // Show iframe when loaded
        $iframe.off('load').on('load', function() {
            $loading.hide();
            $iframe.show();
        });
    }
    
    function loadPackagePricing(packageId) {
        console.log('Loading package pricing for:', packageId);
        $('.upgrade-duration__radios').html('<p><i class="fa fa-spinner fa-spin"></i> Loading pricing...</p>');
        
        $.ajax({
            url: '/api/package/' + packageId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var tiers = [];
                if (data.country_prices && data.country_prices.length > 0 && data.country_prices[0].price_tiers) {
                    tiers = typeof data.country_prices[0].price_tiers === 'string' ? JSON.parse(data.country_prices[0].price_tiers) : data.country_prices[0].price_tiers;
                } else if (data.price_tiers) {
                    tiers = typeof data.price_tiers === 'string' ? JSON.parse(data.price_tiers) : data.price_tiers;
                }
                var html = '';
                if (tiers.length > 0) {
                    tiers.forEach(function(t) {
                        html += '<div class="radio radio--custom my-2"><label class="d-flex align-items-center pl-0"><input class="ml-2" name="duration" type="radio" data-price="' + t.price + '" value="' + t.days + '"><span>' + t.days + ' days for $' + t.price + '</span></label></div>';
                    });
                } else {
                    html = '<p class="text-danger">No pricing available.</p>';
                }
                $('.upgrade-duration__radios').html(html);
            },
            error: function() {
                $('.upgrade-duration__radios').html('<p class="text-danger">Failed to load pricing.</p>');
            }
        });
    }
    
    function initPayPalButtons() {
        var $container = $('#paypal-button-container');
        console.log('=== initPayPalButtons CALLED ===');
        $container.html('<p><i class="fa fa-spinner fa-spin"></i> Loading PayPal...</p>');
        
        if (!selectedPackageId || !selectedDuration || !selectedPrice) {
            $container.html('<p class="text-danger">Please select a duration first.</p>');
            return;
        }
        
        if (typeof paypal !== 'undefined' && paypal.Buttons) {
            console.log('PayPal SDK already loaded');
            renderPayPalButtons($container);
        } else {
            console.log('Loading PayPal SDK...');
            document.querySelectorAll('script[src*="paypal.com/sdk"]').forEach(function(s) { s.remove(); });
            var script = document.createElement('script');
            script.src = 'https://www.paypal.com/sdk/js?client-id=ARoQNQK4S-k3R3g2vLFGtfyGlI7UiA1ZyDkyxrCEtcuDguDoVuSH2_JcWMjowUTwI3jMgjr5ttj8QSrJ&currency=USD';
            script.onload = function() {
                console.log('PayPal SDK loaded');
                setTimeout(function() { renderPayPalButtons($container); }, 300);
            };
            script.onerror = function() {
                $container.html('<p class="text-danger">Failed to load payment system.</p>');
            };
            document.head.appendChild(script);
        }
    }
    
    function renderPayPalButtons($container) {
        console.log('=== renderPayPalButtons ===');
        
        if (typeof paypal === 'undefined' || !paypal.Buttons) {
            $container.html('<p class="text-danger">Payment system unavailable.</p>');
            return;
        }
        
        $container.empty();
        
        setLivewireProperty('selectedPackage', selectedPackageId);
        setLivewireProperty('selectedDuration', selectedDuration);
        setLivewireProperty('selectedAmount', selectedPrice);
        
        try {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{ amount: { value: selectedPrice, currency_code: 'USD' } }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function() {
                        console.log('PayPal approved, calling handlePayPalApproval');
                        var component = getLivewireComponent();
                        if (component) {
                            component.call('handlePayPalApproval', data.orderID);
                        }
                    });
                },
                onError: function(err) {
                    console.error('PayPal Error:', err);
                    alert('Payment failed. Please try again.');
                }
            }).render('#paypal-button-container');
            console.log('PayPal buttons rendered!');
        } catch (err) {
            console.error('Error rendering PayPal:', err);
            $container.html('<p class="text-danger">Failed to initialize payment.</p>');
        }
    }
    
    // Initialize when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initNewProfileUpgradePage);
    } else {
        initNewProfileUpgradePage();
    }
    
    // Re-init on Livewire navigation
    document.addEventListener('livewire:navigated', function() {
        if (window.location.pathname.includes('/new-profile') || window.location.pathname.includes('/upgrade')) {
            window.newProfileUpgradeInitialized = false;
            setTimeout(initNewProfileUpgradePage, 100);
        }
    });
})();
</script>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('profileCreated', (data) => {
        setTimeout(() => {
            window.location.href = '/my-profile/' + data[0].slug + '/' + data[0].id;
        }, 1500);
    });
    
    Livewire.on('showMessage', (data) => {
        if (data[0].type === 'success') {
            alert(data[0].message);
        } else if (data[0].type === 'error') {
            alert('Error: ' + data[0].message);
        }
    });
});
</script>
@endpush
