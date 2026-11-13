{{-- "Upgrade to Premium" popup for the free plan's people limit
     (App\Support\ChatAllowance). Opened when a free user tries to message or
     call a new person past the limit: Livewire components
     dispatch('premium-limit'); fetch-based paths (uploads, calls) call
     window.evShowPremiumLimit() on a premium_required response. Built on
     first use and appended to <body>, outside any Livewire component, so
     re-renders can't close it; rebuilt after wire:navigate swaps the body. --}}
<style>
.ev-plm { position: fixed; inset: 0; z-index: 100050; display: flex; align-items: center; justify-content: center; padding: 20px; background: rgba(0,0,0,.72); animation: evPlmFade .15s ease-out; }
.ev-plm[hidden] { display: none; }
.ev-plm-card { position: relative; width: 100%; max-width: 400px; padding: 30px 26px 22px; border: 1px solid rgba(255,255,255,.08); border-radius: 18px; background: #131616; color: #fff; text-align: center; box-shadow: 0 24px 60px rgba(0,0,0,.55); animation: evPlmRise .18s ease-out; }
.ev-plm-close { position: absolute; top: 12px; right: 12px; width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; border: 0; border-radius: 50%; background: transparent; color: #8a9396; cursor: pointer; }
.ev-plm-close:hover { background: rgba(255,255,255,.06); color: #fff; }
.ev-plm-icon { width: 64px; height: 64px; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; border-radius: 50%; background: rgba(193,241,29,.12); color: #C1F11D; }
.ev-plm-title { margin: 0 0 8px; color: #fff; font-size: 21px; font-weight: 700; line-height: 1.25; }
.ev-plm-text { margin: 0 0 22px; color: #9aa3a6; font-size: 14.5px; line-height: 1.5; }
.ev-plm-cta { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; height: 48px; border-radius: 999px; background: #C1F11D; color: #0a0d0e !important; font-size: 15px; font-weight: 700; text-decoration: none !important; }
.ev-plm-cta:hover { background: #d4ff3d; }
.ev-plm-later { margin-top: 10px; width: 100%; height: 42px; border: 0; border-radius: 999px; background: transparent; color: #9aa3a6; font: inherit; font-size: 14px; font-weight: 600; cursor: pointer; }
.ev-plm-later:hover { background: rgba(255,255,255,.05); color: #fff; }
@keyframes evPlmFade { from { opacity: 0; } to { opacity: 1; } }
@keyframes evPlmRise { from { opacity: 0; transform: translateY(10px) scale(.98); } to { opacity: 1; transform: none; } }
@media (prefers-reduced-motion: reduce) { .ev-plm, .ev-plm-card { animation: none; } }
</style>
<script>
(function () {
    if (window.evShowPremiumLimit) return;
    const FREE_PEOPLE = @json(\App\Support\ChatAllowance::FREE_PEOPLE);
    const UPGRADE_URL = @json(url('/premium-account'));

    function build() {
        const modal = document.createElement('div');
        modal.id = 'evPremiumLimit';
        modal.className = 'ev-plm';
        modal.hidden = true;
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'evPlmTitle');
        modal.innerHTML = `
            <div class="ev-plm-card">
                <button type="button" class="ev-plm-close" data-plm-close aria-label="Close">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
                <div class="ev-plm-icon" aria-hidden="true">
                    <svg width="30" height="27" viewBox="0 0 18 16" fill="currentColor"><path d="M17.43 4.57 14.97 12.16H3.03L.56 4.57l4.87 2.74L9 .56l3.57 6.75 4.86-2.74ZM3.36 14.67h11.28a.75.75 0 0 1 0 1.5H3.36a.75.75 0 0 1 0-1.5Z"/></svg>
                </div>
                <h3 class="ev-plm-title" id="evPlmTitle">Upgrade to Premium</h3>
                <p class="ev-plm-text">Your free plan lets you message and call ${FREE_PEOPLE} people. Upgrade to Premium to talk to as many people as you like.</p>
                <a class="ev-plm-cta" href="${UPGRADE_URL}">Upgrade to Premium</a>
                <button type="button" class="ev-plm-later" data-plm-close>Not now</button>
            </div>`;
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.closest('[data-plm-close]')) close();
        });
        document.body.appendChild(modal);
        return modal;
    }

    function close() {
        const modal = document.getElementById('evPremiumLimit');
        if (modal) modal.hidden = true;
    }

    window.evShowPremiumLimit = function () {
        const modal = document.getElementById('evPremiumLimit') || build();
        modal.hidden = false;
        setTimeout(() => modal.querySelector('.ev-plm-cta')?.focus(), 30);
    };

    // A refused fetch (upload, call signal): true when it was the free plan.
    window.evIsPremiumLimit = function (statusOrBody) {
        if (statusOrBody === 402) return true;
        const text = typeof statusOrBody === 'string' ? statusOrBody : JSON.stringify(statusOrBody || {});
        return text.includes('premium_required');
    };

    window.addEventListener('premium-limit', () => window.evShowPremiumLimit());
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !document.getElementById('evPremiumLimit')?.hidden) close();
    });
})();
</script>
