@push('css')
<style>
    /* ============================================================
       Secure Checkout — iframes the dark-themed hosted checkout
       served by the myads project (/external-payment/checkout).
       myads handles Stripe directly; we just relay postMessage.
       ============================================================ */
    body { background: #000 !important; }
    body > .ev-header-account { display: none !important; }

    .cc-page { background: #000; min-height: 100vh; color: #fff; padding-bottom: 60px; }

    .cc-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 24px; }
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

    .cc-iframe-wrap {
        max-width: 760px;
        margin: 0 auto;
        padding: 0 8px;
        position: relative;
    }
    #premium-gateway-iframe {
        width: 100%;
        min-height: 640px;
        border: none;
        display: block;
        background: #000;
    }
    .cc-iframe-loading {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: rgba(255,255,255,0.75);
        font-size: 14px;
    }
    .cc-iframe-loading .cc-spin {
        width: 28px; height: 28px;
        border: 3px solid rgba(193,241,29,0.25);
        border-top-color: #C1F11D;
        border-radius: 50%;
        animation: cc-spin 0.8s linear infinite;
    }
    @keyframes cc-spin { to { transform: rotate(360deg); } }

    @media (max-width: 640px) {
        #premium-gateway-iframe { min-height: 580px; }
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

        <div class="cc-iframe-wrap">
            <div id="premium-gateway-loading" class="cc-iframe-loading">
                <span class="cc-spin"></span>
                <div>Loading secure checkout…</div>
            </div>
            <iframe id="premium-gateway-iframe" style="display:none;" allow="payment *"></iframe>
        </div>
    </div>
</div>

@push('js')
<script>
(function () {
    // myads host is configurable via config('services.myads.base_url'); fall back
    // to the public domain the user already has deployed.
    const myadsBase    = @js(rtrim(config('services.myads.base_url', 'https://myadsnetwork.com'), '/'));
    const price        = @js((float) $price);
    const planSlug     = @js($plan);
    const planLabel    = @js($duration);
    const period       = @js($period);
    const customerEmail = @js(auth()->user()->email ?? '');
    const customerName  = @js(auth()->user()->name ?? '');

    function initPremiumIframe() {
        const $iframe  = document.getElementById('premium-gateway-iframe');
        const $loading = document.getElementById('premium-gateway-loading');
        if (!$iframe || !$loading) return;

        const referenceId = 'PREMIUM_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        window.premiumPaymentReference = { referenceId, amount: price, plan: planSlug };

        const callbackUrl = encodeURIComponent(
            window.location.origin + '/payment/primary-callback?reference_id=' + referenceId
        );

        const url = myadsBase + '/external-payment/premium-checkout' +
            '?price=' + encodeURIComponent(price) +
            '&package_id=' + encodeURIComponent('premium_' + planSlug) +
            '&package_name=' + encodeURIComponent(planLabel) +
            '&period_label=' + encodeURIComponent(period || '/ month') +
            '&currency=USD' +
            '&callback_url=' + callbackUrl +
            '&reference_id=' + referenceId +
            '&customer_email=' + encodeURIComponent(customerEmail) +
            '&customer_name=' + encodeURIComponent(customerName);

        $iframe.onload = function () {
            $loading.style.display = 'none';
            $iframe.style.display = 'block';
        };
        $iframe.src = url;
    }

    window.addEventListener('message', function (event) {
        // Trust messages only from myads (iframe) or our own callback relay.
        const allowed = [myadsBase, window.location.origin];
        if (allowed.indexOf(event.origin) === -1) return;

        const data = event.data || {};
        const ref  = data.reference_id;
        if (!ref || typeof ref !== 'string' || ref.indexOf('PREMIUM_') !== 0) return;

        if (data.type === 'payment_success') {
            if (window.premiumPaymentProcessed === ref) return;
            window.premiumPaymentProcessed = ref;
            Livewire.dispatch('processPremiumPayment', { referenceId: ref });
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPremiumIframe);
    } else {
        initPremiumIframe();
    }
})();
</script>
@endpush
