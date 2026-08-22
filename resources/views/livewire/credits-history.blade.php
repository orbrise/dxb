<div>
<style>
    .ev-history-wrap {
        max-width: 1200px;
        margin: 24px auto;
        padding: 0 20px;
    }
    .ev-history-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .ev-history-title {
        color: #fff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ev-back-link {
        color: var(--accent, #C1F11D);
        text-decoration: none;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .ev-back-link:hover { text-decoration: underline; }

    .ev-summary-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    .ev-summary-card {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 6px;
        padding: 18px 20px;
    }
    .ev-summary-label {
        color: #999;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 6px 0;
    }
    .ev-summary-value {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }
    .ev-summary-value.credit { color: #22c55e; }
    .ev-summary-value.debit { color: #ef4444; }

    .ev-filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .ev-filter-tab {
        background: transparent;
        border: 1px solid #2a2a2a;
        color: #ccc;
        padding: 6px 16px;
        border-radius: 16px;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .ev-filter-tab:hover { border-color: var(--accent, #C1F11D); color: #fff; }
    .ev-filter-tab.active {
        background: var(--accent, #C1F11D);
        border-color: var(--accent, #C1F11D);
        color: #000;
        font-weight: 600;
    }

    .ev-table-wrap {
        background: #1a1a1a;
        border: 1px solid #2a2a2a;
        border-radius: 6px;
        overflow: hidden;
    }
    .ev-table {
        width: 100%;
        border-collapse: collapse;
        color: #ddd;
        font-size: 14px;
    }
    .ev-table th {
        background: #111;
        color: #999;
        text-align: left;
        padding: 12px 16px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #2a2a2a;
    }
    .ev-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #2a2a2a;
        vertical-align: middle;
    }
    .ev-table tr:last-child td { border-bottom: none; }
    .ev-table tr:hover td { background: #1e1e1e; }

    .ev-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .ev-badge-credit { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .ev-badge-debit { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .ev-badge-completed { background: rgba(34, 197, 94, 0.15); color: #22c55e; }
    .ev-badge-pending { background: rgba(234, 179, 8, 0.15); color: #eab308; }
    .ev-badge-failed { background: rgba(239, 68, 68, 0.15); color: #ef4444; }

    .ev-amount { font-weight: 700; white-space: nowrap; }
    .ev-amount.credit { color: #22c55e; }
    .ev-amount.debit { color: #ef4444; }

    .ev-empty {
        padding: 60px 20px;
        text-align: center;
        color: #999;
    }
    .ev-empty i { font-size: 40px; margin-bottom: 12px; color: #444; }

    .ev-pagination-wrap {
        padding: 16px;
        border-top: 1px solid #2a2a2a;
    }

    @media (max-width: 768px) {
        .ev-summary-row { grid-template-columns: 1fr; }
        .ev-table th, .ev-table td { padding: 10px 8px; font-size: 12px; }
        .ev-hide-mobile { display: none; }
    }
</style>

<div class="ev-history-wrap">
    <div class="ev-history-header">
        <h1 class="ev-history-title">
            <i class="fa fa-history" style="color: var(--accent, #C1F11D);"></i>
            Credits History
        </h1>
        <a href="{{ route('user.account') }}" class="ev-back-link">
            <i class="fa fa-arrow-left"></i> Back to Account
        </a>
    </div>

    <div class="ev-summary-row">
        <div class="ev-summary-card">
            <p class="ev-summary-label">Current Balance</p>
            <p class="ev-summary-value">${{ number_format($walletBalance, 2) }}</p>
        </div>
        <div class="ev-summary-card">
            <p class="ev-summary-label">Total Loaded</p>
            <p class="ev-summary-value credit">+${{ number_format($totalLoaded, 2) }}</p>
        </div>
        <div class="ev-summary-card">
            <p class="ev-summary-label">Total Spent</p>
            <p class="ev-summary-value debit">-${{ number_format($totalSpent, 2) }}</p>
        </div>
    </div>

    <div class="ev-filter-tabs">
        <button type="button" wire:click="$set('filter', 'all')" class="ev-filter-tab {{ $filter === 'all' ? 'active' : '' }}">All</button>
        <button type="button" wire:click="$set('filter', 'credit')" class="ev-filter-tab {{ $filter === 'credit' ? 'active' : '' }}">Loaded (Credits)</button>
        <button type="button" wire:click="$set('filter', 'debit')" class="ev-filter-tab {{ $filter === 'debit' ? 'active' : '' }}">Spent (Debits)</button>
    </div>

    <div class="ev-table-wrap">
        @if($transactions->isEmpty())
            <div class="ev-empty">
                <div><i class="fa fa-receipt"></i></div>
                <p>No transactions yet.</p>
            </div>
        @else
            <table class="ev-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="ev-hide-mobile">Description</th>
                        <th class="ev-hide-mobile">Package</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                        @php
                            $isCredit = in_array($tx->type, $creditTypes);
                            $isDebit = in_array($tx->type, $debitTypes);
                            $typeLabel = match($tx->type) {
                                'credit' => 'Credit',
                                'credit_purchase' => 'Credit Purchase',
                                'debit' => 'Debit',
                                'package_purchase' => 'Package Purchase',
                                'package_upgrade' => 'Package Upgrade',
                                'paypal_payment' => 'PayPal Payment',
                                'primary_gateway_payment' => 'Gateway Payment',
                                default => ucwords(str_replace('_', ' ', $tx->type)),
                            };
                            $statusLabel = ucfirst($tx->status ?? 'completed');
                            $statusClass = match(strtolower($tx->status ?? 'completed')) {
                                'completed', 'success' => 'ev-badge-completed',
                                'pending' => 'ev-badge-pending',
                                'failed', 'declined', 'cancelled' => 'ev-badge-failed',
                                default => 'ev-badge-completed',
                            };
                        @endphp
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($tx->created_at)->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="ev-badge {{ $isCredit ? 'ev-badge-credit' : 'ev-badge-debit' }}">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="ev-hide-mobile">{{ $tx->description ?: '—' }}</td>
                            <td class="ev-hide-mobile">{{ $tx->package->name ?? '—' }}</td>
                            <td class="ev-amount {{ $isCredit ? 'credit' : 'debit' }}">
                                {{ $isCredit ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                            </td>
                            <td>
                                <span class="ev-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            @if($transactions->hasPages())
                <div class="ev-pagination-wrap">
                    {{ $transactions->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
</div>
