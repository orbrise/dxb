{{-- Shared "Buy Credits" form: amount input + payment-method selector +
     primary-gateway iframe + PayPal container + Contact Support (message or
     call the support team to pay another way). Used by:
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

    {{-- JS-driven inline error — same slot as the @error block above, but
         populated by the gateway init functions (initPrimaryGatewayIframe /
         initPayPalButtons) when the amount is below $10. Previously those
         wrote their error into #primary-gateway-loading / #paypal-button-
         container at the bottom of the card, which the user never saw was
         tied to the amount they'd just typed. Hidden by default; JS flips
         display + sets the message. --}}
    <div id="credits-amount-inline-error" class="mt-2" style="display:none; color:#ff4d4d; font-size:13px; align-items:center; gap:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <span id="credits-amount-inline-error-text"></span>
    </div>

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

            <!-- Contact Support Option: pay another way through the support team -->
            <div class="payment-method-option" data-payment-method="support">
                <label for="payment_method_support" class="mb-0">
                    <input type="radio" name="payment_method" id="payment_method_support" value="support">
                    <span>Contact Support</span>
                    <span class="ev-support-hint">
                        Message or call
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 14v-3a9 9 0 0 1 18 0v3"/><path d="M21 15a2 2 0 0 1-2 2h-1v-5h1a2 2 0 0 1 2 2Z"/><path d="M3 15a2 2 0 0 0 2 2h1v-5H5a2 2 0 0 0-2 2Z"/><path d="M18 17v1a3 3 0 0 1-3 3h-3"/></svg>
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

    <!-- Contact Support Section -->
    <div id="support-payment-section" class="payment-section">
        <div class="payment-section-card">
            <p>Don't want to pay by card? Message or call our support team. They'll help you pay another way and add the credits to your wallet.</p>
            <div class="ev-support-actions">
                {{-- Opens the Support chat with a ready-to-send note about the amount
                     (&draft=). Rendered here from $amount so Livewire re-renders keep
                     it; updateSupportMessageLinks() in purchase-credits.blade.php
                     builds the same text while the user types. Plain link: /my-chat
                     always full-loads. --}}
                @php
                    $supportAmount = is_numeric($amount ?? null) && $amount > 0 ? (float) $amount : 0;
                    $supportDraft = $supportAmount > 0
                        ? "Hi, I'd like to add \${$supportAmount} to my wallet. How can I pay?"
                        : "Hi, I'd like to buy credits for my wallet. How can I pay?";
                @endphp
                <a class="ev-support-btn primary" data-support-credits-message
                   href="{{ route('user.chat', ['support' => 1, 'draft' => $supportDraft]) }}"
                   data-base-href="{{ route('user.chat', ['support' => 1]) }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg>
                    Message Support
                </a>
                @if($supportConversationId ?? null)
                    {{-- Rings every support agent (webrtc-scripts handles data-rtc-call). --}}
                    <button type="button" class="ev-support-btn"
                            data-rtc-call="audio" data-rtc-support="1" data-peer-id="0" data-peer-name="Support" data-peer-avatar=""
                            data-conversation-id="{{ $supportConversationId }}">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        Call Support
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
