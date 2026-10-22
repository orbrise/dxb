@push('css')
<style>
    .mm-page { color: #fff; min-height: 100vh; padding: 24px 16px 60px; }
    .mm-head { text-align: center; margin: 10px 0 26px; }
    .mm-head h1 { font-size: 28px; font-weight: 700; margin: 0 0 8px; letter-spacing: -0.3px; }
    .mm-head p  { color: #8e99a0; font-size: 14px; margin: 0; }

    .mm-wrap { max-width: 760px; margin: 0 auto; }

    .mm-card {
        background: #15191B;
        border: 1px solid #23292B;
        border-radius: 14px;
        padding: 24px;
        margin-bottom: 20px;
    }

    .mm-sub-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 22px;
    }
    .mm-sub-label { color: #9aa6ac; font-size: 13px; margin: 0 0 6px; }
    .mm-sub-plan  { color: #fff; font-size: 24px; font-weight: 700; margin: 0; }
    .mm-sub-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(193, 241, 29, 0.4);
        color: #C1F11D;
        background: rgba(193, 241, 29, 0.07);
    }
    .mm-sub-badge--inactive {
        border-color: rgba(255,255,255,0.15);
        color: #9aa6ac;
        background: transparent;
    }

    .mm-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }
    .mm-info {
        background: transparent;
        border: 1px solid #272d2f;
        border-radius: 10px;
        padding: 14px 16px;
    }
    .mm-info-label { color: #8e99a0; font-size: 12px; margin: 0 0 4px; }
    .mm-info-value { color: #fff; font-size: 15px; font-weight: 600; margin: 0; }
    .mm-info-value--accent { color: #C1F11D; }

    .mm-progress-row {
        display: flex; justify-content: space-between; align-items: baseline;
        margin: 18px 0 8px;
        color: #9aa6ac;
        font-size: 13px;
    }
    .mm-progress {
        width: 100%;
        height: 6px;
        border-radius: 999px;
        background: #262c2e;
        overflow: hidden;
    }
    .mm-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #C1F11D 0%, #a8d619 100%);
        border-radius: 999px;
        transition: width 400ms ease;
    }

    .mm-cta-row { text-align: center; margin-top: 24px; }
    .mm-cta {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #C1F11D;
        color: #0D1011;
        padding: 12px 28px;
        border-radius: 999px;
        font-weight: 700;
        text-decoration: none;
        font-size: 14px;
        border: 0;
        cursor: pointer;
        transition: background 150ms;
    }
    .mm-cta:hover { background: #d4f84d; color: #0D1011; }

    /* History */
    .mm-history-title { font-size: 17px; font-weight: 700; margin: 0 0 16px; }
    .mm-history-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 16px;
        border: 1px solid #272d2f;
        border-radius: 10px;
        margin-bottom: 10px;
        gap: 10px;
    }
    .mm-history-row:last-child { margin-bottom: 0; }
    .mm-history-left-plan { color: #fff; font-weight: 600; font-size: 14px; margin: 0 0 2px; }
    .mm-history-left-sub  { color: #8e99a0; font-size: 12px; margin: 0; }
    .mm-history-right     { text-align: right; }
    .mm-history-amount    { color: #C1F11D; font-weight: 700; font-size: 15px; }
    .mm-history-status    { color: #2CE4A5; font-size: 12px; }
    .mm-history-status--refunded { color: #ff6b6b; }
    .mm-history-empty     { color: #8e99a0; font-size: 13px; text-align: center; padding: 18px; }

    .mm-empty-card {
        text-align: center;
        padding: 40px 24px;
    }
    .mm-empty-card h3 { color: #fff; margin: 0 0 8px; }
    .mm-empty-card p  { color: #8e99a0; margin: 0 0 18px; }

    @media (max-width: 540px) {
        .mm-page { padding: 16px 10px 40px; }
        .mm-head h1 { font-size: 22px; }
        .mm-card { padding: 18px; border-radius: 12px; }
        .mm-sub-head { flex-direction: row; align-items: center; }
        .mm-sub-plan { font-size: 20px; }
        .mm-info-grid { grid-template-columns: 1fr; gap: 10px; }
        .mm-cta { width: 100%; justify-content: center; }
    }
</style>
@endpush

<div class="mm-page">
    <div class="mm-wrap">
        <div class="mm-head">
            <h1>Membership Management</h1>
            <p>Review your active subscription, expiration date, and payment history.</p>
        </div>

        {{-- Active Subscription Card --}}
        <div class="mm-card">
            @if($hasActive)
                <div class="mm-sub-head">
                    <div>
                        <p class="mm-sub-label">Active Subscription</p>
                        <h2 class="mm-sub-plan">{{ $planName }}</h2>
                    </div>
                    <span class="mm-sub-badge">Active</span>
                </div>

                <div class="mm-info-grid">
                    <div class="mm-info">
                        <p class="mm-info-label">Purchase Date</p>
                        <p class="mm-info-value">{{ $purchaseDate?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div class="mm-info">
                        <p class="mm-info-label">Expiry Date</p>
                        <p class="mm-info-value">{{ $expiresAt?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                    <div class="mm-info">
                        <p class="mm-info-label">Days Remaining</p>
                        <p class="mm-info-value mm-info-value--accent">
                            {{ $daysRemaining }} {{ \Illuminate\Support\Str::plural('Day', $daysRemaining) }}
                        </p>
                    </div>
                    <div class="mm-info">
                        <p class="mm-info-label">Payment Method</p>
                        <p class="mm-info-value">{{ $paymentMethodLabel }}</p>
                    </div>
                </div>

                <div class="mm-progress-row">
                    <span>Duration Progress</span>
                    <span>{{ $durationPct }}% elapsed</span>
                </div>
                <div class="mm-progress">
                    <div class="mm-progress-bar" style="width: {{ max(1, $durationPct) }}%;"></div>
                </div>

                <div class="mm-cta-row">
                    <a href="{{ url('/premium-account') }}" wire:navigate class="mm-cta">
                        <i class="fa fa-crown"></i> Renew or Change Plan
                    </a>
                </div>
            @else
                <div class="mm-empty-card">
                    <h3>No Active Subscription</h3>
                    <p>Upgrade to Premium to unlock unlimited messaging and chat features.</p>
                    <a href="{{ url('/premium-account') }}" wire:navigate class="mm-cta">
                        <i class="fa fa-crown"></i> View Plans
                    </a>
                </div>
            @endif
        </div>

        {{-- Payment & Renewal History --}}
        <div class="mm-card">
            <h3 class="mm-history-title">Payment &amp; Renewal History</h3>
            @if($history->count() === 0)
                <div class="mm-history-empty">No transactions yet.</div>
            @else
                @foreach($history as $tx)
                    @php
                        $methodLabel = match ($tx->payment_method) {
                            'wallet' => 'Evoory Wallet',
                            'card'   => 'Credit / Debit Card',
                            'free'   => 'Free Plan',
                            default  => ucfirst((string) $tx->payment_method),
                        };
                        $statusClass = $tx->status === 'refunded' ? 'mm-history-status--refunded' : '';
                    @endphp
                    <div class="mm-history-row">
                        <div>
                            <p class="mm-history-left-plan">{{ $tx->plan_name_snapshot }}</p>
                            <p class="mm-history-left-sub">
                                {{ $tx->created_at->format('Y-m-d') }} via {{ $methodLabel }}
                            </p>
                        </div>
                        <div class="mm-history-right">
                            <div class="mm-history-amount">+${{ number_format((float) $tx->amount, 2) }}</div>
                            <div class="mm-history-status {{ $statusClass }}">{{ ucfirst($tx->status) }}</div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
