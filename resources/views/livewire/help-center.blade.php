<div>
<div class="ev-help-page">
    {{-- Header bar: Back link + centered logo --}}
    <div class="ev-help-header">
        <a href="{{ url('/') }}" wire:navigate class="ev-help-back">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            <span>Back</span>
        </a>
        <a href="{{ url('/') }}" wire:navigate class="ev-help-logo">
            @if(isset($setting) && $setting->app_logo)
                <img src="{{ smart_asset($setting->app_logo) }}" alt="{{ $setting->app_name ?? 'evoory' }}">
            @else
                <span>{{ $setting->app_name ?? 'evoory' }}</span>
            @endif
        </a>
        <div class="ev-help-header-spacer"></div>
    </div>

    {{-- Log In / Sign up tab buttons --}}
    <div class="ev-help-tabs">
        <a href="{{ route('sign-in') }}" wire:navigate class="ev-help-tab ev-help-tab--primary">Log In</a>
        <a href="{{ route('register') }}" wire:navigate class="ev-help-tab">Sign up</a>
    </div>

    {{-- Support card --}}
    <div class="ev-help-card">
        <div class="ev-help-card__icon" aria-hidden="true">
           <svg width="29" height="26" viewBox="0 0 29 26" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M14.1294 26V24.375H25.7778V12.2752C25.7778 9.23108 24.6683 6.68904 22.4492 4.64913C20.2291 2.60812 17.5794 1.58763 14.5 1.58763C11.4206 1.58763 8.77089 2.60812 6.55078 4.64913C4.33067 6.69012 3.22115 9.23217 3.22222 12.2752V21.5312H0V13.884H1.61111L1.6385 11.8869C1.6675 10.1915 2.03269 8.62062 2.73406 7.17438C3.43543 5.72813 4.37202 4.46983 5.54383 3.3995C6.71565 2.32917 8.07113 1.495 9.61028 0.897C11.1494 0.299 12.7793 0 14.5 0C16.2207 0 17.8495 0.299 19.3865 0.897C20.9235 1.495 22.2731 2.32808 23.4352 3.39625C24.5974 4.46442 25.5291 5.72108 26.2305 7.16625C26.9319 8.61142 27.3089 10.1823 27.3615 11.8788L27.3889 13.884H29V21.5312H27.3889V26H14.1294ZM9.41695 15.158C9.16991 14.9305 9.04639 14.6488 9.04639 14.313C9.04639 13.9772 9.16991 13.6901 9.41695 13.4517C9.66398 13.2134 9.95398 13.0942 10.2869 13.0942C10.6199 13.0942 10.9094 13.2134 11.1553 13.4517C11.4013 13.6901 11.5248 13.9772 11.5259 14.313C11.527 14.6488 11.4034 14.9305 11.1553 15.158C10.9072 15.3855 10.6172 15.4993 10.2853 15.4993C9.95344 15.4993 9.66398 15.3855 9.41695 15.158ZM17.8447 15.158C17.5976 14.9305 17.4741 14.6488 17.4741 14.313C17.4741 13.9772 17.5976 13.6901 17.8447 13.4517C18.0917 13.2134 18.3817 13.0942 18.7147 13.0942C19.0476 13.0942 19.3371 13.2134 19.5831 13.4517C19.829 13.6901 19.9525 13.9772 19.9536 14.313C19.9547 14.6488 19.8312 14.9305 19.5831 15.158C19.3349 15.3855 19.0449 15.4993 18.7131 15.4993C18.3812 15.4993 18.0917 15.3855 17.8447 15.158ZM5.9885 12.9187C5.84243 10.4856 6.61952 8.41154 8.31978 6.69663C10.019 4.98171 12.1059 4.12425 14.5806 4.12425C16.66 4.12425 18.5031 4.75421 20.1099 6.01413C21.7167 7.27404 22.6973 8.92883 23.0518 10.9785C20.9176 10.9514 18.937 10.4022 17.11 9.33075C15.283 8.25933 13.8813 6.76975 12.905 4.862C12.5162 6.73725 11.7079 8.38067 10.4803 9.79225C9.25154 11.2038 7.75428 12.246 5.9885 12.9187Z" fill="white"/>
</svg>

        </div>
        <h1 class="ev-help-card__title">We're Here to Help</h1>
        <p class="ev-help-card__subtitle">Our support team is ready to assist you 24/7</p>

        {{-- Primary call CTA --}}
        <a href="tel:+15169005003" class="ev-help-call">
            <span class="ev-help-call__row">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                <span class="ev-help-call__number">+1 516 900 5003</span>
            </span>
            <span class="ev-help-call__sub">Call us to get in touch</span>
        </a>

        {{-- Messaging buttons --}}
        <div class="ev-help-chats">
            <a href="https://wa.me/15169005003" target="_blank" rel="noopener" class="ev-help-chat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#25D366" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413"/>
                </svg>
                <span>WhatsApp</span>
            </a>
            <a href="https://t.me/evoory" target="_blank" rel="noopener" class="ev-help-chat">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="#229ED9" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0m5.894 8.221-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.022c.243-.213-.054-.334-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.566-4.458c.538-.196 1.006.128.832.938"/>
                </svg>
                <span>Telegram</span>
            </a>
        </div>

        <div class="ev-help-divider"><span>or</span></div>

        {{-- Email row --}}
        <a href="mailto:support@evoory.com" class="ev-help-email">
            <span class="ev-help-email__icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </span>
            <span class="ev-help-email__text">
                <span class="ev-help-email__label">Email us at</span>
                <span class="ev-help-email__addr">support@evoory.com</span>
            </span>
        </a>

        <p class="ev-help-card__footer">Your satisfaction is our priority</p>
    </div>
</div>

@push('css')
<style>
.ev-help-page {
    max-width: 560px;
    margin: 0 auto;
    padding: 16px;
    color: #fff;
}

/* Header */
.ev-help-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 4px 0 20px;
    gap: 12px;
}
.ev-help-back {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #C1F11D !important;
    text-decoration: none !important;
    font-size: 15px;
    font-weight: 500;
    flex: 0 0 auto;
}
.ev-help-back svg {
    width: 14px !important;
    height: 14px !important;
    transform: none !important;
    -webkit-transform: none !important;
    margin: 0 !important;
}
.ev-help-logo {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
}
.ev-help-logo img {
    height: 28px;
    width: auto;
    display: block;
}
.ev-help-logo span {
    color: #C1F11D;
    font-size: 22px;
    font-weight: 600;
    font-style: italic;
}
.ev-help-header-spacer { flex: 0 0 50px; }

/* Tabs */
.ev-help-tabs {
    display: flex;
    gap: 10px;
    padding: 6px 0 16px;
    border-bottom: 1px solid #33393C;
    margin-bottom: 22px;
    justify-content: center;
}

.ev-help-tab {
    flex: 0 1 auto;
    min-width: 160px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 6px 18px;
    border-radius: 999px;
    border: 1px solid #2a3241;
    background: transparent;
    color: #fff !important;
    text-decoration: none !important;
    font-size: 13px;
    font-weight: 500;
    transition: background 0.15s, border-color 0.15s;
}
.ev-help-tab:hover { background: rgba(193, 241, 29, 0.06); }
.ev-help-tab--primary {
    background: #C1F11D;
    border-color: #C1F11D;
    color: #000 !important;
    font-weight: 600;
}
.ev-help-tab--primary:hover {
    background: #b5e600;
    color: #000 !important;
}

/* Card */
.ev-help-card {
    background: #0e1218;
    border: 1px solid #33393C;
    border-radius: 14px;
    padding: 28px 22px;
    text-align: center;
}
.ev-help-card__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
}
.ev-help-card__icon svg {
    width: 48px !important;
    height: 48px !important;
    transform: none !important;
    -webkit-transform: none !important;
    margin: 0 !important;
    display: block;
}
.ev-help-card__title {
    color: #C1F11D;
    font-size: 18px;
    font-weight: 400;
    margin: 0 0 0px;
}
.ev-help-card__subtitle {
    color: #cfd3d6;
    font-size: 14px;
    margin: 0 0 20px;
}

/* Phone CTA */
.ev-help-call {
    display: block;
    text-align: center;
    background: #1a2010;
    border-radius: 6px;
    padding: 10px 16px;
    margin-bottom: 14px;
    text-decoration: none !important;
    color: #fff !important;
    margin-inline: 20px;
}
.ev-help-call__row {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 2px;
    white-space: nowrap;
}
.ev-help-call__row svg {
    width: 20px !important;
    height: 20px !important;
    transform: none !important;
    -webkit-transform: none !important;
    margin: 0 !important;
}
.ev-help-call__number {
    font-size: 18px;
    font-weight: 400;
    color: #fff;
    white-space: nowrap;
}
.ev-help-call__sub {
    display: block;
    color: #ffffff;
    font-size: 13px;
}

/* Chat buttons row */
.ev-help-chats {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
    margin-inline: 20px;
}
.ev-help-chat {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    border-radius: 10px;
    background: #0a0d12;
    border: 1px solid #2a3241;
    color: #fff !important;
    text-decoration: none !important;
    font-size: 14px;
    font-weight: 500;
}
.ev-help-chat:hover { border-color: #3a4250; }
.ev-help-chat svg {
    width: 20px !important;
    height: 20px !important;
    transform: none !important;
    -webkit-transform: none !important;
    margin: 0 !important;
    display: block;
}

/* "or" divider */
.ev-help-divider {
    display: flex;
    align-items: center;
    color: #ffffff;
    font-size: 13px;
    margin: 12px 20px;
}
.ev-help-divider::before,
.ev-help-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #ffffff;
  
}
.ev-help-divider span { padding: 0 10px; }

/* Email row */
.ev-help-email {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 12px 14px;
    border-radius: 10px;
    text-decoration: none !important;
    color: #fff !important;
    text-align: left;
    margin-inline: 20px;
}
.ev-help-email:hover { border-color: #3a4250; }
.ev-help-email__icon {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: rgba(193, 241, 29, 0.08);
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.ev-help-email__icon svg {
    width: 22px !important;
    height: 22px !important;
    transform: none !important;
    -webkit-transform: none !important;
    margin: 0 !important;
}
.ev-help-email__text {
    display: flex;
    flex-direction: column;
    line-height: 1.3;
}
.ev-help-email__label {
    color: #8b9298;
    font-size: 13px;
}
.ev-help-email__addr {
    color: #fff;
    font-size: 15px;
    font-weight: 500;
}

.ev-help-card__footer {
    margin: 18px 0 0;
    color: #8b9298;
    font-size: 13px;
    text-align: center;
}

/* Hide global app header on this page (its own header lives inside the card) */
body:has(.ev-help-page) #header,
body:has(.ev-help-page) .ev-header { display: none !important; }

/* Narrow screens: drop side margins so rows all line up against the card padding */
@media (max-width: 420px) {
    .ev-help-card { padding: 24px 14px; }
    .ev-help-call,
    .ev-help-chats,
    .ev-help-email { margin-inline: 0; }
    .ev-help-divider { margin: 12px 0; }
    .ev-help-call__number { font-size: 17px; }
}
</style>
@endpush
</div>
