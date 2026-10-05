@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
<style>
    /* ============================================================
       Premium account packages page.
       Matches the dark theme + lime/pink accent palette used across
       the account area. Dedicated class prefix (.pa-*) so it can't
       collide with the existing .acct-* / .ev-* rules.
       ============================================================ */
    body { background: #000 !important; }
    body > .ev-header-account { display: none !important; }

    .pa-page { background: #000; min-height: 100vh; color: #fff; padding-bottom: 60px; }

    /* Back bar — same strip look as /my-account for visual continuity. */
    .pa-back-wrap { background: #131616; padding: 14px 0; margin-bottom: 32px; }
    .pa-back-inner { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
    .pa-back-link {
        color: #C1F11D;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }
    .pa-back-link:hover { color: #d9ff4a; }

    .pa-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    /* Hero header */
    .pa-hero { text-align: center; margin-bottom: 42px; }
    .pa-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        background: rgba(193, 241, 29, 0.14);
        border: 1px solid #C1F11D;
        color: #C1F11D;
        padding: 7px 24px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .pa-badge img { height: 18px; width: auto; display: block; }
    .pa-badge svg { width: 18px; height: 18px; display: block; }
    .pa-title {
        font-size: 44px;
        font-weight: 700;
        line-height: 1.1;
        color: #fff;
        margin: 0 0 16px;
    }
    .pa-subtitle {
        color: rgba(255,255,255,0.55);
        font-size: 15px;
        line-height: 1.6;
        max-width: 640px;
        margin: 0 auto;
    }

    /* Plans grid */
    .pa-plans {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        align-items: stretch;
        margin-top: 48px;
    }

    .pa-card {
        position: relative;
        background: #0F1314;
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        padding: 28px 24px 24px;
        display: flex;
        flex-direction: column;
        min-height: 460px;
    }

    /* Variant borders / glows */
    .pa-card.pa-free {
        background: #1D2224;
        border: 1px solid #3F4546;
    }
    .pa-card.pa-lime {
        background: #131801;
        border: 1px solid #C1F11D;
        box-shadow: 0 20px 60px -20px rgba(193,241,29,0.25);
    }
    .pa-card.pa-pink {
        background: #0F020E;
        border: 1px solid #E557DC;
        box-shadow: 0 20px 60px -20px rgba(229,87,220,0.3);
    }

    /* Top tag pill (MONTHLY / MOST POPULAR) */
    .pa-tag {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        padding: 2px 16px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        white-space: nowrap;
    }
    .pa-tag.pa-tag-lime { background: linear-gradient(90deg, #C1F11D 0%, #0BC47D 100%); color: #0a0a0a; }
    .pa-tag.pa-tag-pink { background: linear-gradient(90deg, #D584D0 0%, #D627FE 100%); color: #fff; }

    /* Plan header row (name + "Current Plan" badge) */
    .pa-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }
    .pa-name {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }
    .pa-cur-badge {
        background: rgba(255,255,255,0.08);
        color: #A6B4B8;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
    }

    /* Price row */
    .pa-price-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        margin-bottom: 14px;
    }
    .pa-price {
        font-size: 38px;
        font-weight: 700;
        color: #fff;
        line-height: 1;
    }
    .pa-period { color: rgba(255,255,255,0.5); font-size: 14px; }

    .pa-desc {
        color: rgba(255,255,255,0.55);
        font-size: 13px;
        line-height: 1.55;
        margin: 0 0 22px;
    }

    /* Feature list */
    .pa-features {
        list-style: none;
        margin: 0 0 28px;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
        flex: 1 1 auto;
    }
    .pa-feature {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        font-size: 13.5px;
    }
    .pa-feature-icon {
        width: 16px;
        height: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .pa-feature-icon svg { display: block; }
    .pa-feature.pa-excluded { color: #FF4A4A; }

    /* CTA button */
    .pa-cta {
        width: 100%;
        padding: 14px 20px;
        border-radius: 999px;
        border: none;
        font-size: 14px;
        font-weight: 700;
        font-family: inherit;
        cursor: pointer;
        transition: transform 120ms, filter 120ms;
    }
    .pa-cta:hover:not(:disabled) { transform: translateY(-1px); filter: brightness(1.08); }
    .pa-cta:disabled { cursor: default; opacity: 0.9; }

    .pa-cta.pa-cta-free {
        background: #343B3E;
        color: rgba(255,255,255,0.5);
    }
    .pa-cta.pa-cta-lime {
        background: #C1F11D;
        color: #0a0a0a;
    }
    .pa-cta.pa-cta-pink {
        background: #D855D0;
        color: #fff;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .pa-plans { grid-template-columns: 1fr; max-width: 460px; margin-left: auto; margin-right: auto; }
        .pa-title { font-size: 34px; }
    }
    @media (max-width: 640px) {
        .pa-title { font-size: 26px; }
        .pa-subtitle { font-size: 13px; }
        .pa-hero { margin-bottom: 28px; }
        .pa-card { padding: 24px 18px 20px; min-height: 0; }
        .pa-price { font-size: 32px; }
    }
</style>
@endpush

<div>
    <div class="pa-page">
        <div class="pa-back-wrap">
            <div class="pa-back-inner">
                <a href="{{ url('/my-account') }}" wire:navigate class="pa-back-link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    Back
                </a>
            </div>
        </div>

        <div class="pa-container">
            {{-- Hero header --}}
            <div class="pa-hero">
                <div class="pa-badge">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M12.7571 9.49951C11.7499 9.47488 10.7665 9.33534 9.87261 8.82888C9.22742 8.46443 9.07393 8.07206 9.32593 7.3924C9.89395 5.86317 12.0651 3.92762 13.99 3.88494C14.8248 3.86688 15.5898 4.05896 16.2374 4.61467C17.2274 5.46589 17.2553 6.88348 16.2982 8.02446C15.3739 9.12521 14.1582 9.53645 12.7571 9.49951Z" fill="#C1F11D"/>
                        <path d="M2.91919 12.9511C2.2139 12.9457 1.5269 12.7261 0.949167 12.3215C0.0815356 11.7223 -0.21479 10.7874 0.157873 9.79744C0.329105 9.31845 0.603399 8.88287 0.961386 8.52148C1.31937 8.16009 1.75232 7.88169 2.22968 7.70594C3.85495 7.08045 5.45806 7.25857 7.00371 7.98584C7.795 8.3585 7.95096 8.81407 7.55039 9.58566C6.74432 11.1379 5.55245 12.2756 3.84592 12.796C3.54795 12.8871 3.22783 12.9019 2.91919 12.9511Z" fill="#C1F11D"/>
                        <path d="M7.40234 12.3089C7.5107 11.8377 7.6486 10.9298 7.93671 10.0704C8.26505 9.0854 8.89135 8.89414 9.80003 9.41374C11.1134 10.1632 12.1805 11.1621 12.7501 12.5986C13.1359 13.5729 13.108 14.5539 12.6262 15.497C11.9917 16.7422 10.7596 17.1453 9.50699 16.514C7.96708 15.7334 7.45652 14.3716 7.40234 12.3089Z" fill="#C1F11D"/>
                        <path d="M9.57444 4.1848C9.52355 5.06228 9.36924 6.04155 8.91203 6.95269C8.53772 7.69719 8.02387 7.90486 7.30153 7.51414C5.77148 6.68509 4.59932 5.52031 4.08629 3.80803C3.79899 2.84929 3.9763 1.93405 4.49671 1.09925C5.17391 0.0157397 6.17452 -0.278942 7.33026 0.269381C8.92844 1.02209 9.54654 2.36992 9.57444 4.1848Z" fill="#C1F11D"/>
                    </svg>
                    Evoory Premium Membership
                </div>
                <h1 class="pa-title">Unlock Unlimited<br>Chat &amp; Calls</h1>
                <p class="pa-subtitle">Connect with anyone, anytime. Enjoy unlimited chats and calls, earn your exclusive Premium badge, and experience Evoory without limits.</p>
            </div>

            {{-- Plans --}}
            <div class="pa-plans">
                @foreach($plans as $plan)
                    <div class="pa-card pa-{{ $plan['variant'] }}">
                        @if($plan['tag'])
                            <span class="pa-tag pa-tag-{{ $plan['tagColor'] }}">{{ $plan['tag'] }}</span>
                        @endif

                        <div class="pa-head">
                            <span class="pa-name">{{ $plan['name'] }}</span>
                            @if($plan['badge'])
                                <span class="pa-cur-badge">{{ $plan['badge'] }}</span>
                            @endif
                        </div>

                        <div class="pa-price-row">
                            <span class="pa-price">{{ $plan['price'] }}</span>
                            <span class="pa-period">{{ $plan['period'] }}</span>
                        </div>

                        <p class="pa-desc">{{ $plan['description'] }}</p>

                        <ul class="pa-features">
                            @foreach($plan['features'] as $feature)
                                <li class="pa-feature {{ $feature['included'] ? 'pa-included' : 'pa-excluded' }}">
                                    <span class="pa-feature-icon">
                                        @if($feature['included'])
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path d="M8 15C6.14348 15 4.36301 14.2625 3.05025 12.9497C1.7375 11.637 1 9.85652 1 8C1 6.14348 1.7375 4.36301 3.05025 3.05025C4.36301 1.7375 6.14348 1 8 1C9.85652 1 11.637 1.7375 12.9497 3.05025C14.2625 4.36301 15 6.14348 15 8C15 9.85652 14.2625 11.637 12.9497 12.9497C11.637 14.2625 9.85652 15 8 15ZM8 16C10.1217 16 12.1566 15.1571 13.6569 13.6569C15.1571 12.1566 16 10.1217 16 8C16 5.87827 15.1571 3.84344 13.6569 2.34315C12.1566 0.842855 10.1217 0 8 0C5.87827 0 3.84344 0.842855 2.34315 2.34315C0.842855 3.84344 0 5.87827 0 8C0 10.1217 0.842855 12.1566 2.34315 13.6569C3.84344 15.1571 5.87827 16 8 16Z" fill="#1CF3A0"/>
                                                <path d="M10.9723 4.96979L10.9523 4.99179L7.47929 9.41679L5.38629 7.32279C5.24412 7.19031 5.05607 7.11819 4.86177 7.12162C4.66747 7.12505 4.48208 7.20376 4.34467 7.34117C4.20726 7.47858 4.12855 7.66397 4.12512 7.85827C4.12169 8.05257 4.19381 8.24062 4.32629 8.38279L6.97229 11.0298C7.04357 11.1009 7.12846 11.157 7.22188 11.1946C7.3153 11.2323 7.41534 11.2507 7.51604 11.2489C7.61674 11.247 7.71603 11.2249 7.80799 11.1838C7.89995 11.1427 7.9827 11.0835 8.05129 11.0098L12.0433 6.01979C12.1792 5.87712 12.2535 5.68669 12.2502 5.48966C12.2468 5.29263 12.1661 5.10484 12.0253 4.96689C11.8846 4.82893 11.6953 4.7519 11.4982 4.75244C11.3012 4.75299 11.1122 4.83106 10.9723 4.96979Z" fill="#1CF3A0"/>
                                            </svg>
                                        @else
                                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 15C9.85652 15 11.637 14.2625 12.9497 12.9497C14.2625 11.637 15 9.85652 15 8C15 6.14348 14.2625 4.36301 12.9497 3.05025C11.637 1.7375 9.85652 1 8 1C6.14348 1 4.36301 1.7375 3.05025 3.05025C1.7375 4.36301 1 6.14348 1 8C1 9.85652 1.7375 11.637 3.05025 12.9497C4.36301 14.2625 6.14348 15 8 15ZM8 16C10.1217 16 12.1566 15.1571 13.6569 13.6569C15.1571 12.1566 16 10.1217 16 8C16 5.87827 15.1571 3.84344 13.6569 2.34315C12.1566 0.842855 10.1217 0 8 0C5.87827 0 3.84344 0.842855 2.34315 2.34315C0.842855 3.84344 0 5.87827 0 8C0 10.1217 0.842855 12.1566 2.34315 13.6569C3.84344 15.1571 5.87827 16 8 16Z" fill="#FF4A4A"/>
                                                <path d="M11.8557 4.85378C11.9468 4.75948 11.9972 4.63318 11.9961 4.50208C11.9949 4.37099 11.9424 4.24558 11.8496 4.15288C11.7569 4.06017 11.6315 4.00759 11.5004 4.00645C11.3693 4.00531 11.243 4.05571 11.1487 4.14678L8.00174 7.29278L4.85574 4.14678C4.80961 4.09903 4.75444 4.06094 4.69344 4.03473C4.63244 4.00853 4.56683 3.99474 4.50044 3.99416C4.43405 3.99358 4.36821 4.00623 4.30676 4.03137C4.24531 4.05651 4.18949 4.09364 4.14254 4.14059C4.09559 4.18753 4.05847 4.24336 4.03333 4.30481C4.00819 4.36626 3.99554 4.4321 3.99611 4.49849C3.99669 4.56488 4.01048 4.63049 4.03669 4.69149C4.06289 4.75249 4.10098 4.80766 4.14874 4.85378L7.29474 7.99978L4.14874 11.1458C4.05485 11.2395 4.00205 11.3667 4.00196 11.4994C4.00187 11.6321 4.05448 11.7594 4.14824 11.8533C4.24199 11.9472 4.3692 12 4.50188 12.0001C4.63457 12.0002 4.76185 11.9475 4.85574 11.8538L8.00174 8.70678L11.1487 11.8538C11.2426 11.9475 11.3699 12.0002 11.5026 12.0001C11.6353 12 11.7625 11.9472 11.8562 11.8533C11.95 11.7594 12.0026 11.6321 12.0025 11.4994C12.0024 11.3667 11.9496 11.2395 11.8557 11.1458L8.70974 7.99978L11.8557 4.85378Z" fill="#FF4A4A"/>
                                            </svg>
                                        @endif
                                    </span>
                                    <span>{{ $feature['label'] }}</span>
                                </li>
                            @endforeach
                        </ul>

                        @if($plan['ctaDisabled'])
                            <button type="button"
                                    class="pa-cta pa-cta-{{ $plan['variant'] }}"
                                    disabled>
                                {{ $plan['cta'] }}
                            </button>
                        @else
                            <a href="{{ url('/premium-account/checkout/' . $plan['id']) }}"
                               wire:navigate
                               class="pa-cta pa-cta-{{ $plan['variant'] }}"
                               style="display:inline-flex; align-items:center; justify-content:center; text-decoration:none;">
                                {{ $plan['cta'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
