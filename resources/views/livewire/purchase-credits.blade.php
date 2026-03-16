<style>
/* Sub-header bar */
.purchase-credits-subheader {
    background: #131616;
    padding: 12px 0;
   
}
.purchase-credits-subheader .ev-container {
    display: flex;
    align-items: center;
    position: relative;
}
.purchase-credits-subheader .back-link {
    color: #C1F11D;
    text-decoration: none;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.purchase-credits-subheader .back-link:hover {
    color: #d4f84d;
}
.purchase-credits-subheader .page-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
}

#header .nav-bar { background: #131616 !important; }
#header { margin-bottom: 0px !important; }
.backclass { background: #0a0a0a !important; }

/* Amount input */
.purchase-credits-page .amount-input-group {
    display: inline-flex;
    align-items: center;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 8px;
    overflow: hidden;
}

.purchase-credits-page .amount-input-group .dollar-sign {
    padding: 10px 14px;
    background: #222;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
    border-right: 1px solid #2a2a2a;
}

.purchase-credits-page .amount-input-group input {
    background: #1a1a1a !important;
    border: none !important;
    color: #fff !important;
    font-size: 16px;
    padding: 10px 14px;
    width: 100px;
    outline: none;
    -moz-appearance: textfield;
}

.purchase-credits-page .amount-input-group input::-webkit-outer-spin-button,
.purchase-credits-page .amount-input-group input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.purchase-credits-page .choose-amount-label {
    color: #999;
    font-size: 15px;
    margin-left: 15px;
}

/* Payment Method Card */
.purchase-credits-page .payment-methods-card {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 24px;
    margin-top: 24px;
    margin-bottom: 20px;
}

.purchase-credits-page .payment-methods-card h2 {
    color: #fff;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 20px;
}

.purchase-credits-page .payment-method-option {
    padding: 16px 20px;
    border: 2px solid #2a2a2a;
    border-radius: 8px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #111;
}

.purchase-credits-page .payment-method-option:hover {
    border-color: #444;
    background: #1a1a1a;
}

.purchase-credits-page .payment-method-option.selected {
    border-color: #C1F11D !important;
    background: #1a1a1a !important;
}

.purchase-credits-page .payment-method-option img {
    height: 28px;
    margin-right: 8px;
}

.purchase-credits-page .payment-method-option input[type="radio"] {
    accent-color: #C1F11D;
    width: 18px;
    height: 18px;
    margin-right: 15px;
}

.purchase-credits-page .payment-method-option label {
    color: #fff !important;
    cursor: pointer;
    margin-bottom: 0;
    font-size: 15px;
}

/* Payment Section Card */
.purchase-credits-page .payment-section-card {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 24px;
    margin-top: 20px;
    color: #fff;
}

.purchase-credits-page .payment-section-card p {
    color: #ccc;
}

.purchase-credits-page .payment-section-card strong {
    color: #fff;
}

/* Package display */
.purchase-credits-page .package-display {
    background: #111;
    border: 1px solid #2a2a2a;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    margin-bottom: 20px;
}

.purchase-credits-page .package-display .package-label {
    color: #C1F11D;
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 8px;
}

.purchase-credits-page .package-display .package-amount {
    color: #fff;
    font-size: 32px;
    font-weight: 700;
}

.purchase-credits-page .package-display .package-currency {
    color: #999;
    font-size: 16px;
    font-weight: 400;
}

#paypal-button-container {
    margin-top: 20px;
    width: 100%;
}

/* Override PayPal button color */
#paypal-button-container .paypal-button {
    background-color: #C1F11D !important;
    border-radius: 22px !important;
}
#paypal-button-container .paypal-button-color-gold,
#paypal-button-container .paypal-button-color-black {
    background: #C1F11D !important;
}
#paypal-button-container .paypal-button:hover {
    background-color: #d4f84d !important;
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

/* Modal */
#paymentResultModal {
    display: none !important;
}

#paymentResultModal.show {
    display: block !important;
}

#paymentResultModal .modal-content {
    background: #14141A !important;
    border: 1px solid #222 !important;
    color: #fff;
}

.modal-backdrop {
    display: none !important;
}
</style>

<div>
<div class="purchase-credits-subheader">
  <div class="ev-container">
    <a href="/female-escorts-in-dubai" class="back-link">&lsaquo; Escorts in Dubai</a>
    <span class="page-title">Purchase Credits</span>
  </div>
</div>

<div class="purchase-credits-page" style="background: #000; min-height: 100vh; padding: 30px 50px;">
  <div style="max-width: 560px;">
        <div id="content">
            <!-- Payment Result Modal -->
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
                  <button type="button" class="close pale px-3 pt-2 position-absolute" style="top:0; color:#fff;" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <div class="modal-body p-4">
                    <div class="sr-only" id="paymentResultModalLabel">Payment Result Status</div>
                    <p class="p-0 mb-0 mt-2 fw-bold">Your payment failed: declined <br>Please recheck your card details and try again. </p>
                  </div>
                  <div class="modal-footer pt-0">
                    <button class="btn btn-default" data-dismiss="modal" style="float: left; background: #C1F11D; color: #000; border: none; border-radius: 22px; padding: 8px 24px;" type="button">Close</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Page Header -->
            <div class="mb-2">
              <h1 class="mt-2" style="color: #fff; font-size: 28px; font-weight: 700;">Purchase Credits</h1>
              <p style="color: #999; font-size: 15px;">Once you have credits in your account, Account Balance will be available as a payment method for upgrading your profiles.</p>
            </div>

            <div>
              <div id="credits_purchase_form">
                <!-- Amount Input -->
                <div class="d-flex align-items-center">
                  <div class="amount-input-group">
                    <span class="dollar-sign">$</span>
                    <input type="number" wire:model.live="amount" min="10" max="100" maxlength="5" pattern="[0-9]*" inputmode="numeric" data-validations="numericality presence" size="5" value="100" id="amount" />
                  </div>
                  <span class="choose-amount-label">Choose amount</span>
                </div>
                @error('amount')
                <div class="mt-2" style="color: #ff4d4d; font-size: 14px;">{{ $message }}</div>
                @enderror

                <!-- Payment Method -->
                <div class="payment-methods-card">
                  <h2>Payment Method</h2>
                  <div class="payment-method-selector">
                    <!-- Primary Gateway Option -->
                    <div class="payment-method-option d-flex align-items-center" data-payment-method="primary">
                      <input type="radio" name="payment_method" id="payment_method_primary" value="primary">
                      <label for="payment_method_primary" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                        <span style="margin-right: 15px;">Primary Gateway</span>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 40px;">
                        <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 45px;">
                      </label>
                    </div>

                    <!-- Secondary Gateway Option -->
                    <div class="payment-method-option d-flex align-items-center" data-payment-method="paypal">
                      <input type="radio" name="payment_method" id="payment_method_paypal" value="paypal">
                      <label for="payment_method_paypal" class="mb-0 d-flex align-items-center" style="flex-grow: 1; cursor: pointer;">
                        <span style="margin-right: 15px;">Secondary Gateway</span>
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard" style="width: 40px;">
                        <img src="{{smart_asset('assets/images/visa.svg')}}" alt="Visa" style="width: 45px;">
                      </label>
                    </div>
                  </div>
                </div>

                <!-- Primary Gateway Payment Section -->
                <div id="primary-payment-section" class="payment-section">
                  <div class="payment-section-card">
                    <p>You will be charged <strong>$<span class="payment-amount">{{ $amount }}</span></strong> via credit/debit card.</p>

                    <div class="package-display">
                      <div class="package-label">Package</div>
                      <div class="package-amount">$<span class="payment-amount">{{ $amount }}</span>.00 <span class="package-currency">USD</span></div>
                    </div>

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
                <div id="paypal-payment-section" class="payment-section">
                  <div class="payment-section-card">
                    <p>You will be charged <strong>$<span class="payment-amount">{{ $amount }}</span></strong> via PayPal.</p>

                    <div class="package-display">
                      <div class="package-label">Package</div>
                      <div class="package-amount">$<span class="payment-amount">{{ $amount }}</span>.00 <span class="package-currency">USD</span></div>
                    </div>

                    <div id="paypal-button-container" class="mt-3"></div>
                    <p class="small text-center mt-3" style="color: #666;">
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
                    shape: 'pill',
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