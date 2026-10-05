@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ============================================================
       Card Secure Checkout — form entry for Primary / Secondary gateway.
       Class prefix .cc-* to avoid colliding with .pm-* / .pc-* / .pa-*.
       ============================================================ */
    body { background: #000 !important; }
    body > .ev-header-account { display: none !important; }

    .cc-page { background: #000; min-height: 100vh; color: #fff; padding-bottom: 60px; }

    .cc-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 40px; }
    .cc-back-link {
        color: #C1F11D;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .cc-back-link:hover { color: #d9ff4a; }

    .cc-container {
        max-width: 720px;
        margin: 0 auto;
        padding: 0 16px;
    }

    .cc-card {
        background: #1D2224;
        border: 1px solid #3F4546;
        border-radius: 14px;
        padding: 28px 32px 32px;
    }

    .cc-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding-bottom: 22px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 24px;
    }
    .cc-title { font-size: 22px; font-weight: 700; color: #fff; margin: 0 0 0px; }
    .cc-sub   { color: rgba(255,255,255,0.5); font-size: 13px; margin: 0; }
    .cc-price-wrap { text-align: right; white-space: nowrap; }
    .cc-price  { font-size: 32px; font-weight: 700; color: #fff; line-height: 1; }
    .cc-period { color: rgba(255,255,255,0.5); font-size: 14px; margin-left: 4px; }

    /* Form fields */
    .cc-field { margin-bottom: 18px; }
    .cc-label {
        display: block;
        color: rgba(255,255,255,0.85);
        font-size: 13px;
        font-weight: 400;
        margin-bottom: 8px;
    }
    .cc-input-wrap {
        position: relative;
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        transition: border-color 120ms;
    }
    .cc-input-wrap:focus-within { border-color: #C1F11D; }
    .cc-input {
        flex: 1 1 auto;
        background: transparent;
        border: none;
        outline: none;
        color: #fff;
        font-size: 15px;
        font-family: inherit;
        font-variant-numeric: tabular-nums;
        letter-spacing: 0.3px;
        min-width: 0;
    }
    .cc-input::placeholder { color: rgba(255,255,255,0.3); }
    .cc-brand {
        flex: 0 0 auto;
        margin-left: 8px;
        display: inline-flex;
        align-items: center;
    }
    .cc-brand img { height: 14px; width: auto; display: block; }

    /* Row: Expiry + CVC side-by-side */
    .cc-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    /* Pay button */
    .cc-pay-wrap { margin-top: 10px; display: flex; justify-content: center; }
    .cc-pay-btn {
        width: 60%;
        padding: 14px 20px;
        border-radius: 999px;
        border: none;
        background: #C1F11D;
        color: #0a0a0a;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-family: inherit;
        transition: filter 120ms, transform 120ms;
    }
    .cc-pay-btn:hover:not(:disabled) { filter: brightness(1.08); transform: translateY(-1px); }
    .cc-pay-btn svg { width: 15px; height: 17px; }

    @media (max-width: 640px) {
        .cc-card { padding: 22px 20px 24px; }
        .cc-head { flex-direction: column; align-items: flex-start; gap: 10px; padding-bottom: 18px; margin-bottom: 20px; }
        .cc-price-wrap { text-align: left; }
        .cc-title { font-size: 18px; }
        .cc-price { font-size: 26px; }
        .cc-pay-btn { width: 100%; }
    }
</style>
@endpush

<div>
    <div class="cc-page">
        <div class="cc-back-wrap">
            <div class="ev-container">
                <a href="{{ url('/premium-account/payment/' . $plan) }}" wire:navigate class="cc-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div class="cc-container">
            <div class="cc-card">
                <div class="cc-head">
                    <div>
                        <h2 class="cc-title">Secure Checkout</h2>
                        <p class="cc-sub">{{ $duration }}</p>
                    </div>
                    <div class="cc-price-wrap">
                        <span class="cc-price">${{ number_format($price, 2) }}</span>
                        <span class="cc-period">{{ $period }}</span>
                    </div>
                </div>

                <div class="cc-field">
                    <label class="cc-label" for="cc_name">Cardholder Name</label>
                    <div class="cc-input-wrap">
                        <input type="text" id="cc_name" class="cc-input"
                               wire:model.defer="cardName"
                               placeholder="Name on card"
                               autocomplete="cc-name">
                    </div>
                </div>

                <div class="cc-field">
                    <label class="cc-label" for="cc_number">Card Number</label>
                    <div class="cc-input-wrap">
                        <input type="text" id="cc_number" class="cc-input"
                               wire:model.defer="cardNumber"
                               placeholder="4242 4242 4242 4242"
                               inputmode="numeric"
                               autocomplete="cc-number"
                               maxlength="23">
                        <span class="cc-brand">
                            <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" alt="VISA">
                        </span>
                    </div>
                </div>

                <div class="cc-row">
                    <div class="cc-field">
                        <label class="cc-label" for="cc_exp">Expiry Date</label>
                        <div class="cc-input-wrap">
                            <input type="text" id="cc_exp" class="cc-input"
                                   wire:model.defer="expiry"
                                   placeholder="MM/YY"
                                   inputmode="numeric"
                                   autocomplete="cc-exp"
                                   maxlength="5">
                        </div>
                    </div>
                    <div class="cc-field">
                        <label class="cc-label" for="cc_cvc">CVC / CVV</label>
                        <div class="cc-input-wrap">
                            <input type="text" id="cc_cvc" class="cc-input"
                                   wire:model.defer="cvc"
                                   placeholder="•••"
                                   inputmode="numeric"
                                   autocomplete="cc-csc"
                                   maxlength="4">
                        </div>
                    </div>
                </div>

                <div class="cc-pay-wrap">
                    <button type="button" class="cc-pay-btn" wire:click="pay">
                        <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M7 0C8.23768 0 9.42466 0.511733 10.2998 1.42262C11.175 2.33352 11.6667 3.56895 11.6667 4.85714V7.28571L11.9047 7.29786C12.4794 7.35919 13.0118 7.64025 13.3989 8.08663C13.7859 8.53302 14.0001 9.11299 14 9.71429V14.5714C14 15.2155 13.7542 15.8332 13.3166 16.2887C12.879 16.7441 12.2855 17 11.6667 17H2.33333C1.71449 17 1.121 16.7441 0.683417 16.2887C0.245833 15.8332 0 15.2155 0 14.5714V9.71429C0 9.07019 0.245833 8.45247 0.683417 7.99703C1.121 7.54158 1.71449 7.28571 2.33333 7.28571V4.85714C2.33333 3.56895 2.825 2.33352 3.70017 1.42262C4.57534 0.511733 5.76232 0 7 0ZM7 9.71429C6.69058 9.71429 6.39383 9.84222 6.17504 10.0699C5.95625 10.2977 5.83333 10.6065 5.83333 10.9286V13.3571C5.83333 13.6792 5.95625 13.988 6.17504 14.2158C6.39383 14.4435 6.69058 14.5714 7 14.5714C7.30942 14.5714 7.60616 14.4435 7.82496 14.2158C8.04375 13.988 8.16667 13.6792 8.16667 13.3571V10.9286C8.16667 10.6065 8.04375 10.2977 7.82496 10.0699C7.60616 9.84222 7.30942 9.71429 7 9.71429ZM7 2.42857C6.38116 2.42857 5.78767 2.68444 5.35008 3.13988C4.9125 3.59533 4.66667 4.21305 4.66667 4.85714V7.28571H9.33333V4.85714C9.33333 4.21305 9.0875 3.59533 8.64991 3.13988C8.21233 2.68444 7.61884 2.42857 7 2.42857Z" fill="black"/>
                        </svg>
                        Pay ${{ number_format($price, 2) }} &amp; Activate Premium
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
