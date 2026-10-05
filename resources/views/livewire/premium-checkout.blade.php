@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ============================================================
       Secure Checkout screen for a Premium plan.
       Class prefix .pc-* so it can't collide with .pa-* / .acct-*.
       ============================================================ */
    body { background: #000 !important; }
    body > .ev-header-account { display: none !important; }

    .pc-page { background: #000; min-height: 100vh; color: #fff; padding-bottom: 60px; }

    .pc-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 48px; }
    /* Inherit the layout's .ev-container sizing so the Back link lines up
       exactly with the "test" logo in the header above. */
    .pc-back-link {
        color: #C1F11D;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .pc-back-link:hover { color: #d9ff4a; }

    .pc-container {
        max-width: 720px;
        margin: 0 auto;
        padding: 0 16px;
    }

    .pc-card {
        background: #1D2224;
        border: 1px solid #3F4546;
        border-radius: 14px;
        padding: 28px 32px 32px;
    }

    /* Header row: left title + right price */
    .pc-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        padding-bottom: 22px;
        border-bottom: 1px solid rgba(255,255,255,0.06);
        margin-bottom: 24px;
    }
    .pc-title {
        font-size: 22px;
        font-weight: 700;
        color: #fff;
        margin: 0 0 4px;
    }
    .pc-sub {
        color: rgba(255,255,255,0.5);
        font-size: 13px;
        margin: 0;
    }
    .pc-price-wrap {
        text-align: right;
        white-space: nowrap;
    }
    .pc-price {
        font-size: 32px;
        font-weight: 700;
        color: #fff;
        line-height: 1;
    }
    .pc-period {
        color: rgba(255,255,255,0.5);
        font-size: 14px;
        margin-left: 4px;
    }

    /* Payment method option (radio-style tile) */
    .pc-method {
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
    .pc-method:hover { border-color: rgba(255,255,255,0.2); }
    .pc-method.is-active {
        border-color: #C1F11D;
        background: rgba(193,241,29,0.06);
        color: #C1F11D;
    }
    .pc-method .pc-radio {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 1.5px solid rgba(255,255,255,0.35);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .pc-method.is-active .pc-radio {
        border-color: #C1F11D;
    }
    .pc-method.is-active .pc-radio::after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #C1F11D;
    }
    .pc-method .pc-method-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        color: inherit;
    }
    .pc-method .pc-method-icon i { font-size: 16px; }

    /* Wallet breakdown table */
    .pc-breakdown {
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        padding: 14px 18px;
        margin-top: 6px;
        margin-bottom: 22px;
    }
    .pc-row {
        display: flex;
        justify-content: space-between;
        padding: 6px 0;
        color: rgba(255,255,255,0.75);
        font-size: 14px;
    }
    .pc-row .pc-val { color: #fff; font-weight: 600; }
    .pc-row.plan .pc-val { color: #1CF3A0; }

    /* Primary CTA — 60% card width, centered. */
    .pc-pay-btn {
        width: 60%;
        margin: 0 auto;
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
    .pc-pay-btn:hover:not(:disabled) { filter: brightness(1.08); transform: translateY(-1px); }
    .pc-pay-btn:disabled { opacity: 0.5; cursor: not-allowed; }
    .pc-pay-btn svg { width: 15px; height: 15px; }

    .pc-insufficient {
        color: #FF4A4A;
        font-size: 12px;
        margin-top: 8px;
        text-align: center;
    }

    @media (max-width: 640px) {
        .pc-card { padding: 22px 20px 24px; border-radius: 10px; }
        .pc-head { flex-direction: column; align-items: flex-start; gap: 10px; padding-bottom: 18px; margin-bottom: 20px; }
        .pc-price-wrap { text-align: left; }
        .pc-title { font-size: 18px; }
        .pc-price { font-size: 26px; }
        .pc-pay-btn { width: 100%; }
    }
</style>
@endpush

<div>
    <div class="pc-page">
        <div class="pc-back-wrap">
            <div class="ev-container">
                <a href="{{ url('/premium-account') }}" wire:navigate class="pc-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div class="pc-container">
            <div class="pc-card">
                {{-- Header: plan name + price --}}
                <div class="pc-head">
                    <div>
                        <h2 class="pc-title">Secure Checkout</h2>
                        <p class="pc-sub">{{ $duration }}</p>
                    </div>
                    <div class="pc-price-wrap">
                        <span class="pc-price">${{ number_format($price, 2) }}</span>
                        <span class="pc-period">{{ $period }}</span>
                    </div>
                </div>

                {{-- Payment-method toggle runs entirely in Alpine so it's instant
                     client-side — the server round-trip was taking 7–8s on prod.
                     The chosen method is only sent to the server when the user
                     clicks Pay (see `$wire.proceed(method)` below). --}}
                <div x-data="{ method: @js($paymentMethod), canUseWallet: @js($canUseWallet) }">
                    <button type="button"
                            class="pc-method"
                            :class="method === 'card' ? 'is-active' : ''"
                            @click="method = 'card'">
                        <span class="pc-radio"></span>
                        <span class="pc-method-icon">
                            <svg width="17" height="13" viewBox="0 0 17 13" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M0 2.16667C0 1.59203 0.223883 1.04093 0.622398 0.634602C1.02091 0.228273 1.56141 0 2.125 0H14.875C15.4386 0 15.9791 0.228273 16.3776 0.634602C16.7761 1.04093 17 1.59203 17 2.16667V3.25H0V2.16667ZM0 5.41667V10.8333C0 11.408 0.223883 11.9591 0.622398 12.3654C1.02091 12.7717 1.56141 13 2.125 13H14.875C15.4386 13 15.9791 12.7717 16.3776 12.3654C16.7761 11.9591 17 11.408 17 10.8333V5.41667H0ZM3.1875 7.58333H4.25C4.53179 7.58333 4.80204 7.69747 5.0013 7.90063C5.20056 8.1038 5.3125 8.37935 5.3125 8.66667V9.75C5.3125 10.0373 5.20056 10.3129 5.0013 10.516C4.80204 10.7192 4.53179 10.8333 4.25 10.8333H3.1875C2.90571 10.8333 2.63546 10.7192 2.4362 10.516C2.23694 10.3129 2.125 10.0373 2.125 9.75V8.66667C2.125 8.37935 2.23694 8.1038 2.4362 7.90063C2.63546 7.69747 2.90571 7.58333 3.1875 7.58333Z" fill="white"/>
                            </svg>
                        </span>
                        <span>Credit / Debit Card</span>
                    </button>

                    <button type="button"
                            class="pc-method"
                            :class="method === 'wallet' ? 'is-active' : ''"
                            @click="method = 'wallet'">
                        <span class="pc-radio"></span>
                        <span class="pc-method-icon">
                            <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path d="M9.21 0.29506C9.082 0.185656 8.93453 0.10389 8.77612 0.054495C8.61771 0.00509994 8.4515 -0.0109431 8.28712 0.00729448C8.12274 0.0255321 7.96344 0.077688 7.81847 0.160743C7.67349 0.243799 7.5457 0.356104 7.4425 0.491158L3.8225 5.23412H5.4225L8.41875 1.30693L9.38125 2.12923L6.96875 5.23412H8.57875L10.35 2.95415L12.4975 4.78832L12.125 5.23412H12.5C12.8728 5.23371 13.2435 5.29143 13.6 5.40538C13.7362 5.1382 13.7795 4.82987 13.7225 4.53303C13.6655 4.23619 13.5117 3.96925 13.2875 3.77776L9.21 0.29506ZM1.25 5.88778C1.25 5.71442 1.31585 5.54815 1.43306 5.42557C1.55027 5.30298 1.70924 5.23412 1.875 5.23412H2.5725L3.56625 3.9268H1.875C1.37772 3.9268 0.900806 4.1334 0.549175 4.50115C0.197544 4.86891 0 5.36769 0 5.88778V13.7317C0 14.5985 0.32924 15.4298 0.915291 16.0427C1.50134 16.6557 2.2962 17 3.125 17H12.5C13.163 17 13.7989 16.7245 14.2678 16.2342C14.7366 15.7438 15 15.0788 15 14.3854V9.15608C15 8.46263 14.7366 7.79759 14.2678 7.30725C13.7989 6.81691 13.163 6.54144 12.5 6.54144H1.875C1.70924 6.54144 1.55027 6.47257 1.43306 6.34998C1.31585 6.2274 1.25 6.06114 1.25 5.88778ZM10.625 11.7707H11.875C12.0408 11.7707 12.1997 11.8396 12.3169 11.9622C12.4342 12.0848 12.5 12.251 12.5 12.4244C12.5 12.5977 12.4342 12.764 12.3169 12.8866C12.1997 13.0092 12.0408 13.078 11.875 13.078H10.625C10.4592 13.078 10.3003 13.0092 10.1831 12.8866C10.0658 12.764 10 12.5977 10 12.4244C10 12.251 10.0658 12.0848 10.1831 11.9622C10.3003 11.8396 10.4592 11.7707 10.625 11.7707Z" fill="#C1F11D"/>
                            </svg>
                        </span>
                        <span>Evoory Wallet</span>
                    </button>

                    {{-- Wallet breakdown — Alpine x-show so toggling is instant. --}}
                    <div class="pc-breakdown" x-show="method === 'wallet'" x-cloak>
                        <div class="pc-row">
                            <span>Your Wallet Balance</span>
                            <span class="pc-val">${{ number_format($walletBalance, 2) }}</span>
                        </div>
                        <div class="pc-row plan">
                            <span>Plan Price</span>
                            <span class="pc-val">${{ number_format($price, 2) }}</span>
                        </div>
                        <div class="pc-row">
                            <span>Remaining Balance After</span>
                            <span class="pc-val">${{ number_format($remainingAfter, 2) }}</span>
                        </div>
                    </div>

                    <button type="button" class="pc-pay-btn"
                            @click="$wire.proceed(method)"
                            :disabled="method === 'wallet' && !canUseWallet">
                        <svg width="14" height="17" viewBox="0 0 14 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M7 0C8.23768 0 9.42466 0.511733 10.2998 1.42262C11.175 2.33352 11.6667 3.56895 11.6667 4.85714V7.28571L11.9047 7.29786C12.4794 7.35919 13.0118 7.64025 13.3989 8.08663C13.7859 8.53302 14.0001 9.11299 14 9.71429V14.5714C14 15.2155 13.7542 15.8332 13.3166 16.2887C12.879 16.7441 12.2855 17 11.6667 17H2.33333C1.71449 17 1.121 16.7441 0.683417 16.2887C0.245833 15.8332 0 15.2155 0 14.5714V9.71429C0 9.07019 0.245833 8.45247 0.683417 7.99703C1.121 7.54158 1.71449 7.28571 2.33333 7.28571V4.85714C2.33333 3.56895 2.825 2.33352 3.70017 1.42262C4.57534 0.511733 5.76232 0 7 0ZM7 9.71429C6.69058 9.71429 6.39383 9.84222 6.17504 10.0699C5.95625 10.2977 5.83333 10.6065 5.83333 10.9286V13.3571C5.83333 13.6792 5.95625 13.988 6.17504 14.2158C6.39383 14.4435 6.69058 14.5714 7 14.5714C7.30942 14.5714 7.60616 14.4435 7.82496 14.2158C8.04375 13.988 8.16667 13.6792 8.16667 13.3571V10.9286C8.16667 10.6065 8.04375 10.2977 7.82496 10.0699C7.60616 9.84222 7.30942 9.71429 7 9.71429ZM7 2.42857C6.38116 2.42857 5.78767 2.68444 5.35008 3.13988C4.9125 3.59533 4.66667 4.21305 4.66667 4.85714V7.28571H9.33333V4.85714C9.33333 4.21305 9.0875 3.59533 8.64991 3.13988C8.21233 2.68444 7.61884 2.42857 7 2.42857Z" fill="black"/>
                        </svg>
                        Pay ${{ number_format($price, 2) }} &amp; Activate Premium
                    </button>

                    <p class="pc-insufficient" x-show="method === 'wallet' && !canUseWallet" x-cloak>
                        Insufficient wallet balance. Top up your wallet or switch to Credit / Debit Card.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
