@section('headerform')
<div class="nav-bar navbar-top-nav">
    <div class="container-fluid">
      <a class="back-link" href="/female-escorts-in-dubai">
        <i class="fa fa-angle-left fa-fw"></i>
        <span class="hidden-xs">Escorts in Dubai</span>
      </a>
      <div class="title">
        <h1>
          <a href="/my-profile/credits/purchase">Purchase Credits</a> 
        </h1>
      </div>
    </div>
  </div>
@endsection

@push('css')
<style>
    .payment-method-option {
        padding: 17px;
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
    
    .payment-method-option img {
        height: 30px;
        margin-right: 10px;
    }
    
    .payment-method-option input[type="radio"] {
        accent-color: #ffc439;
        width: 18px;
        height: 18px;
        margin-right: 15px;
    }
    
    .payment-method-option label {
        color: #ffffff !important;
        cursor: pointer;
        margin-bottom: 0;
    }
    
    #paypal-button-container {
        margin-top: 20px;
        width: 100%;
    }
    
    /* Primary Gateway Iframe */
    #primary-gateway-iframe {
        width: 100%;
        min-height: 450px;
        border: none;
        background: #fff;
        border-radius: 8px;
    }
    
    #primary-gateway-loading {
        text-align: center;
        padding: 40px;
        color: #fff;
    }
    
    .payment-section {
        display: none;
    }
    
    /* Ensure modal is hidden by default */
    #paymentResultModal {
        display: none !important;
    }
    
    #paymentResultModal.show {
        display: block !important;
    }
    
    .modal-backdrop {
        display: none !important;
    }
</style>
@endpush

<div class="container-fluid">
    <div class="content-wrapper no-sidebar">
        <div id="content">
            <div aria-labelledby="paymentResultModalLabel" class="modal fade modal__payment-result" id="paymentResultModal" role="dialog" tabindex="-1" style="display: none;">
              <div class="modal-dialog" role="document" style="margin-top:100px">
                <div class="modal-content p-2 border-0">
                  <div class="d-flex justify-content-center">
                    <div class="status-illustration status-error d-flex justify-content-center align-items-center">
                      <svg width="12" height="46" id="exclamation" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 46">
                        <polygon points="6 30.73 6 30.72 6 30.73 6 30.73" fill="#fff" />
                        <path d="M8.16,0H3.84c-.09,0-.18,0-.28,0C1.5.11-.1,1.87,0,3.94l2.16,24.68c.19,1.96,1.86,3.45,3.84,3.41,1.97.04,3.64-1.44,3.84-3.41L12,3.94c0-.09,0-.18,0-.28C11.95,1.59,10.23-.05,8.16,0Z" fill="#fff" />
                        <path d="M6,35c-3.04,0-5.5,2.46-5.5,5.5h0c0,3.04,2.46,5.5,5.5,5.5s5.5-2.46,5.5-5.5c0-3.04-2.46-5.5-5.5-5.5Z" fill="#fff" />
                      </svg>
                    </div>
                  </div>
                  <button type="button" class="close pale px-3 pt-2 position-absolute" style="top:0" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <div class="modal-body p-4">
                    <div class="sr-only" id="paymentResultModalLabel">Payment Result Status</div>
                    <p class="p-0 mb-0 mt-2 fw-bold">Your payment failed: declined <br>Please recheck your card details and try again. </p>
                  </div>
                  <div class="modal-footer pt-0">
                    <button class="btn btn-default" data-dismiss="modal" style="float: left" type="button">Close</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-sm-12 mb-2">
              <h1 class="mt-2">Purchase Credits</h1>
              <p>Once you have credits in your account, Account Balance will be available as a payment method for upgrading your profiles.</p>
            </div>
            <div class="col-md-6">
              <div class="simple_form validate margin-bottom" id="credits_purchase_form">
                <div class="row">
                  <div class="col-sm-12">
                    <div class="form-group d-flex align-items-center">
                      <label class="large-font mb-0 ml-3" for="amount" style="order:1">Choose amount</label>
                      <div class="input-group">
                        <div class="input-group-addon">$ </div>
                        <input type="number" wire:model.live="amount" class="string optional form-control small validate" min="10" max="100" maxlength="5" pattern="[0-9]*" inputmode="numeric" data-validations="numericality presence" size="5" value="100" id="amount" />
                      </div>
                    </div>
                    @error('amount')
                    <div class="text-danger small  mb-1" style="font-size:14px">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                <div class="row d-flex flex-column">
                  <div class="col-sm-12 px-0 block mb-3" id="payment-methods-block">
                    <h2 class="payment-options-box__title d-flex align-items-center p-3 mb-0">
                      Payment Method
                    </h2>
                    <div class="payment-method-selector p-3">
                      <!-- Primary Gateway Option (Stripe) -->
                      <div class="payment-method-option d-flex align-items-center" data-payment-method="primary">
                        <input type="radio" name="payment_method" id="payment_method_primary" value="primary">
                        <label for="payment_method_primary" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                          <span style="font-size: 16px; margin-right: 15px;">Primary Gateway</span>
                          <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 40px;">
                          <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 45px;">
                        </label>
                      </div>
                      
                      <!-- PayPal Option -->
                      <div class="payment-method-option d-flex align-items-center" data-payment-method="paypal">
                        <input type="radio" name="payment_method" id="payment_method_paypal" value="paypal">
                        <label for="payment_method_paypal" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                          <span style="font-size: 16px; margin-right: 15px;">Secondary Gateway</span>
                           <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 40px;">
                          <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 45px;">
                        </label>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Primary Gateway Payment Section (Stripe via iframe) -->
                  <div id="primary-payment-section" class="block p-0 mb-3 payment-section" style="display: none;">
                    <div class="p-3">
                      <p>You will be charged <strong>$<span class="payment-amount">{{ $amount }}</span></strong> via credit/debit card.</p>
                      <div id="primary-gateway-container">
                        <div id="primary-gateway-loading" style="display: none;">
                          <i class="fa fa-spinner fa-spin fa-2x"></i>
                          <p class="mt-2">Loading secure payment form...</p>
                        </div>
                        <iframe id="primary-gateway-iframe" style="display: none;"></iframe>
                      </div>
                    </div>
                  </div>
                  
                  <!-- PayPal Payment Section -->
                  <div id="paypal-payment-section" class="block p-0 mb-3 payment-section" style="display: none; background: #f0f0f0;">
                    <div class="p-3" style="color:#4e4e4e">
                      <p>You will be charged <strong>$<span class="payment-amount">{{ $amount }}</span></strong> via PayPal.</p>
                      <div id="paypal-button-container" class="mt-3"></div>
                      <p class="small text-muted text-center mt-3" style="color:#4e4e4e">
                        Secure payment processing by PayPal. You can use your credit/debit card or PayPal balance.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
(function() {
    // Prevent multiple initializations
    if (window.purchaseCreditsInitialized) return;
    window.purchaseCreditsInitialized = true;
    
    var selectedAmount = {{ $amount }};
    var selectedPaymentMethod = null;
    
    function initPurchaseCreditsPage() {
        console.log('=== initPurchaseCreditsPage CALLED ===');
        
        // Update amount from input
        var amountInput = document.getElementById('amount');
        if (amountInput) {
            selectedAmount = parseFloat(amountInput.value) || {{ $amount }};
        }
        
        // Payment method selection
        document.querySelectorAll('.payment-method-option').forEach(function(option) {
            option.addEventListener('click', function(e) {
                var method = this.getAttribute('data-payment-method');
                
                // Update selection UI
                document.querySelectorAll('.payment-method-option').forEach(function(opt) {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
                this.querySelector('input[type="radio"]').checked = true;
                
                // Hide all payment sections
                document.querySelectorAll('.payment-section').forEach(function(section) {
                    section.style.display = 'none';
                });
                
                // Show selected payment section
                selectedPaymentMethod = method;
                var section = document.getElementById(method + '-payment-section');
                if (section) {
                    section.style.display = 'block';
                    
                    // Scroll to payment section on mobile
                    if (window.innerWidth <= 768) {
                        setTimeout(function() {
                            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    }
                }
                
                // Initialize the appropriate payment method
                if (method === 'primary') {
                    initPrimaryGatewayIframe();
                } else if (method === 'paypal') {
                    initPayPalButtons();
                }
            });
        });
        
        // Amount input change handler
        if (amountInput) {
            amountInput.addEventListener('input', function() {
                selectedAmount = parseFloat(this.value) || 0;
                updateAmountDisplay(selectedAmount);
                
                // Reinitialize payment method if valid amount
                if (selectedAmount >= 10 && selectedAmount <= 100) {
                    if (selectedPaymentMethod === 'primary') {
                        clearTimeout(window.primaryInitTimeout);
                        window.primaryInitTimeout = setTimeout(initPrimaryGatewayIframe, 500);
                    } else if (selectedPaymentMethod === 'paypal') {
                        clearTimeout(window.paypalInitTimeout);
                        window.paypalInitTimeout = setTimeout(initPayPalButtons, 500);
                    }
                }
            });
        }
        
        // Listen for message from iframe (payment success)
        window.addEventListener('message', function(event) {
            if (event.origin !== 'https://myadsnetwork.com' && event.origin !== window.location.origin) return;
            
            if (event.data && event.data.type === 'payment_success') {
                var ref = event.data.reference_id;
                
                // Prevent duplicate processing
                if (window.primaryPaymentProcessed && window.primaryPaymentProcessed === ref) {
                    console.log('Payment already processed, ignoring duplicate message');
                    return;
                }
                window.primaryPaymentProcessed = ref;
                
                // Show success in iframe container
                document.getElementById('primary-gateway-iframe').style.display = 'none';
                document.getElementById('primary-gateway-loading').innerHTML = '<i class="fa fa-check-circle fa-2x text-success"></i><p class="mt-2">Payment successful! Processing...</p>';
                document.getElementById('primary-gateway-loading').style.display = 'block';
                
                // Process the payment in Livewire
                Livewire.dispatch('processPrimaryPayment', { 
                    amount: parseFloat(window.primaryPaymentReference.amount),
                    referenceId: ref
                });
            }
        });
        
        // Livewire message handler
        window.addEventListener('showMessage', function(e) {
            var d = e.detail[0];
            alert(d.message);
            if (d.type === 'success') {
                window.location.reload();
            }
        });
        
        // Don't auto-initialize - let user select payment method
    }
    
    function updateAmountDisplay(amount) {
        document.querySelectorAll('.payment-amount').forEach(function(el) {
            el.textContent = amount;
        });
    }
    
    function initPrimaryGatewayIframe() {
        console.log('=== initPrimaryGatewayIframe CALLED ===');
        
        var $loading = document.getElementById('primary-gateway-loading');
        var $iframe = document.getElementById('primary-gateway-iframe');
        
        var amountInput = document.getElementById('amount');
        if (amountInput) {
            selectedAmount = parseFloat(amountInput.value) || 0;
        }
        
        if (selectedAmount < 10 || selectedAmount > 100) {
            document.getElementById('primary-gateway-container').innerHTML = '<p class="text-danger p-3">Please enter a valid amount between $10 and $100.</p>';
            return;
        }
        
        // Show loading
        $loading.style.display = 'block';
        $iframe.style.display = 'none';
        
        // Generate a unique reference ID
        var referenceId = 'CREDITS_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        
        // Store reference for callback
        window.primaryPaymentReference = {
            referenceId: referenceId,
            amount: selectedAmount
        };
        
        // Build the external payment URL
        var callbackUrl = encodeURIComponent(window.location.origin + '/payment/credits-callback?reference_id=' + referenceId);
        var cancelUrl = encodeURIComponent(window.location.href);
        
        var externalPaymentUrl = 'https://myadsnetwork.com/external-payment/checkout' +
            '?price=' + selectedAmount +
            '&name=' + encodeURIComponent('Purchase ' + selectedAmount + ' Credits') +
            '&currency=USD' +
            '&callback_url=' + callbackUrl +
            '&reference_id=' + referenceId +
            '&customer_email=' + encodeURIComponent('{{ auth()->user()->email }}') +
            '&embed=1';
        
        console.log('Loading iframe:', externalPaymentUrl);
        
        // Set iframe source
        $iframe.src = externalPaymentUrl;
        
        // Show iframe when loaded
        $iframe.onload = function() {
            $loading.style.display = 'none';
            $iframe.style.display = 'block';
        };
    }
    
    function initPayPalButtons() {
        console.log('=== initPayPalButtons CALLED ===');
        
        var $container = document.getElementById('paypal-button-container');
        $container.innerHTML = '<p><i class="fa fa-spinner fa-spin"></i> Loading PayPal...</p>';
        
        var amountInput = document.getElementById('amount');
        if (amountInput) {
            selectedAmount = parseFloat(amountInput.value) || 0;
        }
        
        if (selectedAmount < 10 || selectedAmount > 100) {
            $container.innerHTML = '<p class="text-danger">Please enter a valid amount between $10 and $100.</p>';
            return;
        }
        
        // Load PayPal SDK if not already loaded
        if (typeof paypal !== 'undefined' && paypal.Buttons) {
            renderPayPalButtons($container);
        } else {
            // Remove any existing PayPal scripts
            document.querySelectorAll('script[src*="paypal.com/sdk"]').forEach(function(s) { s.remove(); });
            
            var script = document.createElement('script');
            script.src = 'https://www.paypal.com/sdk/js?client-id=ARoQNQK4S-k3R3g2vLFGtfyGlI7UiA1ZyDkyxrCEtcuDguDoVuSH2_JcWMjowUTwI3jMgjr5ttj8QSrJ&currency=USD';
            script.onload = function() {
                console.log('PayPal SDK loaded');
                setTimeout(function() { renderPayPalButtons($container); }, 300);
            };
            script.onerror = function() {
                $container.innerHTML = '<p class="text-danger">Failed to load PayPal.</p>';
            };
            document.head.appendChild(script);
        }
    }
    
    function renderPayPalButtons($container) {
        console.log('=== renderPayPalButtons ===');
        
        if (typeof paypal === 'undefined' || !paypal.Buttons) {
            $container.innerHTML = '<p class="text-danger">PayPal unavailable.</p>';
            return;
        }
        
        $container.innerHTML = '';
        
        var amountInput = document.getElementById('amount');
        var currentAmount = amountInput ? parseFloat(amountInput.value) : selectedAmount;
        
        try {
            paypal.Buttons({
                style: {
                    layout: 'vertical',
                    color: 'gold',
                    shape: 'rect',
                    label: 'paypal',
                    height: 40
                },
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: currentAmount,
                                currency_code: 'USD'
                            },
                            description: 'Purchase ' + currentAmount + ' credits'
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        console.log('PayPal approved:', data.orderID);
                        $container.innerHTML = '<div class="alert alert-info">Processing your payment...</div>';
                        
                        // Call Livewire to handle payment
                        Livewire.dispatch('handlePayPalApproval', { orderId: data.orderID });
                    });
                },
                onError: function(err) {
                    console.error('PayPal Error:', err);
                    alert('Payment failed. Please try again.');
                },
                onCancel: function() {
                    console.log('Payment cancelled');
                }
            }).render('#paypal-button-container');
            
            console.log('PayPal buttons rendered!');
        } catch (err) {
            console.error('Error rendering PayPal:', err);
            $container.innerHTML = '<p class="text-danger">Failed to initialize PayPal.</p>';
        }
    }
    
    // Initialize when ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPurchaseCreditsPage);
    } else {
        initPurchaseCreditsPage();
    }
    
    // Re-init on Livewire navigation
    document.addEventListener('livewire:navigated', function() {
        if (window.location.pathname.includes('purchase-credits')) {
            window.purchaseCreditsInitialized = false;
            setTimeout(initPurchaseCreditsPage, 100);
        }
    });
})();
</script>
@endpush