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
        <div class="amount-input-group" style="position:relative;">
            <span class="dollar-sign">$</span>
            {{-- Blank by default (no value=""). Users must type their own amount;
                 min $10 enforced client-side (HTML5 min) + server-side (Livewire
                 rules). Placeholder shows "0" as a visual cue that the field
                 starts empty, mirroring WhatsApp/Stripe-style amount inputs.
                 debounce.500ms stops the min-10 error from flashing on
                 every keystroke while the user is still typing a 2-digit
                 amount like "15" (would fire "min 10" at "1" otherwise). --}}
            <input type="number" wire:model.live.debounce.500ms="amount" min="10" step="1" pattern="[0-9]*" inputmode="numeric" data-validations="numericality presence" size="5" placeholder="0" id="amount"
                   @error('amount') style="border:1px solid #ff4d4d;" @enderror />
        </div>
        <span class="choose-amount-label">Choose amount</span>
    </div>
    {{-- Field-level error — renders directly under the input + label row,
         right where the user is looking. Was previously a toast-at-bottom
         for the "Pay with less than $10" path (processPrimaryPayment); both
         flows now funnel into this one block via addError('amount', …). --}}
    @error('amount')
        <div class="mt-2" style="color:#ff4d4d; font-size:13px; display:flex; align-items:center; gap:6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $message }}</span>
        </div>
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
