@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ============================================================
       Select Payment Method screen — step after Secure Checkout.
       Class prefix .pm-* to avoid colliding with .pa-* / .pc-*.
       ============================================================ */
    body { background: #000 !important; }
    body > .ev-header-account { display: none !important; }

    .pm-page { background: #000; min-height: 100vh; color: #fff; padding-bottom: 60px; }

    .pm-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 40px; }
    .pm-back-link {
        color: #C1F11D;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .pm-back-link:hover { color: #d9ff4a; }

    .pm-container {
        max-width: 720px;
        margin: 0 auto;
        padding: 0 16px;
    }

    .pm-hero { text-align: center; margin-bottom: 32px; }
    .pm-title {
        font-size: 28px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 10px;
    }
    .pm-subtitle {
        color: rgba(255,255,255,0.55);
        font-size: 14px;
        line-height: 1.55;
        max-width: 500px;
        margin: 0 auto;
    }

    .pm-card {
        background: #1D2224;
        border: 1px solid #3F4546;
        border-radius: 5px;
        padding: 24px;
        margin-bottom: 20px;
    }
    .pm-card.pm-paypal-card {
        background: #0D1011;
        border: none;
    }
    .pm-card-title {
        font-size: 17px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 18px;
    }

    /* Gateway radio tile */
    .pm-gateway {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        font-family: inherit;
        width: 100%;
        text-align: left;
        margin-bottom: 10px;
        transition: border-color 120ms, background 120ms;
    }
    .pm-gateway:last-child { margin-bottom: 0; }
    .pm-gateway:hover { border-color: rgba(255,255,255,0.2); }
    .pm-gateway.is-active {
        border-color: #C1F11D;
        background: rgba(193,241,29,0.06);
    }
    .pm-gateway .pm-radio {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.35);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .pm-gateway.is-active .pm-radio { border-color: #C1F11D; }
    .pm-gateway.is-active .pm-radio::after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #C1F11D;
    }
    .pm-gateway-label { flex: 0 0 auto; }
    .pm-gateway-logos { display: inline-flex; align-items: center; gap: 6px; flex: 0 0 auto; margin-left: 4px; }

    .pm-logo-mc { height: 16px; width: auto; display: block; }
    .pm-logo-visa { height: 12px; width: auto; display: block; }

    /* Charge summary line */
    .pm-charge {
        color: #fff;
        font-size: 14px;
        text-align: center;
        margin: 0 0 16px;
    }

    /* PayPal-style buttons */
    .pm-pay-btn {
        width: 100%;
        padding: 14px 18px;
        border-radius: 5px;
        border: none;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-family: inherit;
        margin-bottom: 10px;
        transition: filter 120ms, transform 120ms;
    }
    .pm-pay-btn:hover:not(:disabled) { filter: brightness(1.05); transform: translateY(-1px); }

    .pm-pay-btn.pm-paypal { background: #C1F11D; color: #003087; }
    .pm-pay-btn.pm-paylater { background: #C1F11D; color: #003087; }
    .pm-pay-btn.pm-card { background: #343B3E; color: #fff; }
    .pm-pay-btn.pm-card svg { color: #fff; }

    .pm-paypal-mark { display: inline-flex; align-items: baseline; font-weight: 900; font-style: italic; }
    .pm-paypal-mark .pp-pal { color: #003087; }
    .pm-paypal-mark .pp-pal2 { color: #009cde; margin-left: 1px; }

    .pm-powered {
        text-align: center;
        font-size: 11px;
        color: rgba(255,255,255,0.4);
        margin: 10px 0 14px;
        display: inline-flex;
        align-items: baseline;
        gap: 4px;
        width: 100%;
        justify-content: center;
    }
    .pm-fine {
        color: rgba(255,255,255,0.4);
        font-size: 11px;
        text-align: center;
        line-height: 1.5;
        margin: 0;
    }

    @media (max-width: 640px) {
        .pm-title { font-size: 22px; }
        .pm-subtitle { font-size: 13px; }
        .pm-card { padding: 18px; border-radius: 10px; }
    }
</style>
@endpush

<div>
    <div class="pm-page">
        <div class="pm-back-wrap">
            <div class="ev-container">
                <a href="{{ url('/premium-account/checkout/' . $plan) }}" wire:navigate class="pm-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div class="pm-container">
            <div class="pm-hero">
                <h1 class="pm-title">Select Payment Method</h1>
                <p class="pm-subtitle">Choose your payment method to activate your Premium membership and unlock unlimited conversations.</p>
            </div>

            {{-- Gateway choice --}}
            <div class="pm-card">
                <h2 class="pm-card-title">Payment Method</h2>

                <a href="{{ url('/premium-account/card/' . $plan . '/primary') }}" wire:navigate
                   class="pm-gateway {{ $gateway === 'primary' ? 'is-active' : '' }}"
                   style="text-decoration:none;">
                    <span class="pm-radio"></span>
                    <span class="pm-gateway-label">Primary Gateway</span>
                    <span class="pm-gateway-logos">
                        <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" alt="Mastercard" class="pm-logo-mc">
                        <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" alt="VISA" class="pm-logo-visa">
                    </span>
                </a>

                <a href="{{ url('/premium-account/card/' . $plan . '/secondary') }}" wire:navigate
                   class="pm-gateway {{ $gateway === 'secondary' ? 'is-active' : '' }}"
                   style="text-decoration:none;">
                    <span class="pm-radio"></span>
                    <span class="pm-gateway-label">Secondary Gateway</span>
                    <span class="pm-gateway-logos">
                        <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/mc_logo-1fee638879a55506111eef88a8369601147f17d09fa23d940350fee69fb9fc79.svg" alt="Mastercard" class="pm-logo-mc">
                        <img src="https://d257pz9kz95xf4.cloudfront.net/assets/icons/visa_logo-5f6bf07538a0b32cedb6babb58d8c28c7a917c26d4d7df3edd61be4980ddef6c.svg" alt="VISA" class="pm-logo-visa">
                    </span>
                </a>
            </div>

            {{-- PayPal pay buttons --}}
            <div class="pm-card pm-paypal-card">
                <p class="pm-charge">You will be charged ${{ number_format($price, 2) }} via PayPal.</p>

                <button type="button" class="pm-pay-btn pm-paypal">
                    <span class="pm-paypal-mark"><span class="pp-pal">Pay</span><span class="pp-pal2">Pal</span></span>
                </button>

                <button type="button" class="pm-pay-btn pm-paylater">
                    <span class="pm-paypal-mark" style="margin-right:4px;"><span class="pp-pal">Pay</span><span class="pp-pal2">Pal</span></span>
                    Pay Later
                </button>

                <button type="button" class="pm-pay-btn pm-card">
                    <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M1 9C1 5.229 1 3.343 2.172 2.172C3.344 1.001 5.229 1 9 1H13C16.771 1 18.657 1 19.828 2.172C20.999 3.344 21 5.229 21 9C21 12.771 21 14.657 19.828 15.828C18.656 16.999 16.771 17 13 17H9C5.229 17 3.343 17 2.172 15.828C1.001 14.656 1 12.771 1 9Z" stroke="currentColor" stroke-width="2"/>
                        <path d="M9 13H5M13 13H11.5M1 7H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Debit or Credit Card
                </button>

                <div class="pm-powered">
                    <span>Powered by</span>
                    <span class="pm-paypal-mark"><span class="pp-pal">Pay</span><span class="pp-pal2">Pal</span></span>
                </div>

                <p class="pm-fine">Secure payment processing by PayPal. You can use your credit/debit card or PayPal balance.</p>
            </div>
        </div>
    </div>
</div>
