@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ============================================================
       Premium activation success screen.
       Class prefix .ps-* to avoid colliding with other premium pages.
       ============================================================ */
    body { background: #000 !important; }
    body > .ev-header-account { display: none !important; }

    .ps-page { background: #000; min-height: 100vh; color: #fff; padding-bottom: 60px; }

    .ps-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 40px; }
    .ps-back-link {
        color: #C1F11D;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .ps-back-link:hover { color: #d9ff4a; }

    .ps-container {
        max-width: 640px;
        margin: 0 auto;
        padding: 0 16px;
    }

    .ps-card {
        background: #1D2224;
        border: 1px solid #3F4546;
        border-radius: 5px;
        padding: 36px 36px 32px;
        text-align: center;
    }

    /* Green check icon (hosted image). */
    .ps-check {
        display: inline-block;
        margin: 0 auto 0px;
    }
    .ps-check img { width: 110px; height: auto; display: block; }

    .ps-title {
        font-size: 24px;
        font-weight: 500;
        color: #fff;
        margin: 0 0 10px;
    }
    .ps-subtitle {
        color: white;
        font-size: 14px;
        line-height: 1.55;
        margin: 0 auto 24px;
        max-width: 460px;
    }

    /* Summary box */
    .ps-summary {
        background: #14141A2E;
        border: 1px solid #5E6365;
        border-radius: 5px;
        padding: 16px 20px;
        margin-bottom: 22px;
        text-align: left;
    }
    .ps-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        color: rgba(255,255,255,0.75);
        font-size: 14px;
    }
    .ps-row .ps-val { color: #fff; font-weight: 600; }
    .ps-row.ps-status .ps-val {
        color: #22c55e;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .ps-row.ps-status .ps-val svg { width: 14px; height: 14px; }

    /* Buttons — 60% width, centered. */
    .ps-btn {
        width: 60%;
        padding: 7px 20px;
        border-radius: 999px;
        border: none;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        display: block;
        margin: 0 auto 10px;
        text-align: center;
        text-decoration: none;
        font-family: inherit;
        transition: filter 120ms, transform 120ms, background 120ms;
    }
    .ps-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
    .ps-btn.ps-primary { background: #C1F11D; color: #0a0a0a; }
    .ps-btn.ps-secondary { background: #343B3E; color: #fff; }
    .ps-btn.ps-secondary:hover { background: #3f4749; }

    @media (max-width: 640px) {
        .ps-card { padding: 28px 20px 24px; }
        .ps-title { font-size: 20px; }
        .ps-subtitle { font-size: 13px; }
        .ps-check img { width: 90px; }
        .ps-btn { width: 100%; }
    }
</style>
@endpush

<div>
    <div class="ps-page">
        <div class="ps-back-wrap">
            <div class="ev-container">
                <a href="{{ url('/my-account') }}" wire:navigate class="ps-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div class="ps-container">
            <div class="ps-card">
                <div class="ps-check" aria-hidden="true">
                    <img src="https://assets.evoory.com/assets/images/tick.png" alt="">
                </div>

                <h1 class="ps-title">You're now Premium!</h1>
                <p class="ps-subtitle">Your Premium membership has been activated successfully.<br>You can now start unlimited conversations with anyone on Evoory.</p>

                <div class="ps-summary">
                    <div class="ps-row">
                        <span>Plan</span>
                        <span class="ps-val">{{ $planLabel }} (${{ number_format($planPrice, 2) }})</span>
                    </div>
                    <div class="ps-row ps-status">
                        <span>Status</span>
                        <span class="ps-val">
                            Active
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="5 12 10 17 20 7"/></svg>
                        </span>
                    </div>
                    <div class="ps-row">
                        <span>Conversations</span>
                        <span class="ps-val">Unlimited</span>
                    </div>
                </div>

                <a href="{{ url('/my-chat') }}" wire:navigate class="ps-btn ps-primary">Start Chatting Now</a>
                <a href="{{ url('/my-account') }}" wire:navigate class="ps-btn ps-secondary">Go to Account</a>
            </div>
        </div>
    </div>
</div>
