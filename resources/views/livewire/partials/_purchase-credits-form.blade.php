{{-- Shared "Buy Credits" form: amount input + payment-method selector +
     primary-gateway iframe + PayPal container. Used by:
       - resources/views/livewire/purchase-credits.blade.php  (standalone page)
       - resources/views/livewire/wallet.blade.php            (modal body)
     The JS that wires it (payment method clicks, iframe URL build, postMessage
     relay) lives at the bottom of purchase-credits.blade.php and uses
     document-level event delegation so it survives Alpine re-mounts of the
     modal. --}}
<div id="credits_purchase_form">
    <!-- Amount Input -->
    <div class="d-flex align-items-center">
        <div class="amount-input-group">
            <span class="dollar-sign">$</span>
            <input type="number" wire:model.live="amount" min="5" step="1" pattern="[0-9]*" inputmode="numeric" data-validations="numericality presence" size="5" value="10" id="amount" />
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
            <div class="payment-method-option" data-payment-method="primary">
                <label for="payment_method_primary" class="mb-0">
                    <input type="radio" name="payment_method" id="payment_method_primary" value="primary">
                    <span>Primary Gateway</span>
                    <span class="ev-card-logos">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard">
                        <img src="{{ smart_asset('assets/images/visa.svg') }}" alt="Visa">
                    </span>
                </label>
            </div>

            <!-- Secondary Gateway Option -->
            <div class="payment-method-option" data-payment-method="paypal">
                <label for="payment_method_paypal" class="mb-0">
                    <input type="radio" name="payment_method" id="payment_method_paypal" value="paypal">
                    <span>Secondary Gateway</span>
                    <span class="ev-card-logos">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b7/MasterCard_Logo.svg" alt="Mastercard">
                        <img src="{{ smart_asset('assets/images/visa.svg') }}" alt="Visa">
                    </span>
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
