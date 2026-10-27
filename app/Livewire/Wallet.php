<?php

namespace App\Livewire;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class Wallet extends Component
{
    public string $txFilter = 'all';

    /**
     * How many transactions are currently shown. Starts at PAGE_SIZE and
     * grows by PAGE_SIZE each time the view fires loadMoreTransactions()
     * (triggered by the IntersectionObserver on the sentinel at the bottom
     * of the list). Reset to PAGE_SIZE whenever the filter changes so the
     * user doesn't scroll into a half-loaded "Credits out" list that still
     * has 100 entries loaded from "All".
     */
    public int $txShown = 20;

    private const PAGE_SIZE = 20;

    public function mount(): void
    {
        // Google-signup / legacy accounts can land here without a wallet row;
        // create one up front so the view's `$user->wallet->balance` is never null.
        $user = Auth::user();
        if ($user && !$user->wallet) {
            $user->wallet()->create(['balance' => 0]);
            $user->load('wallet');
        }
    }

    public function setFilter(string $filter): void
    {
        $this->txFilter = in_array($filter, ['all', 'credit', 'debit'], true) ? $filter : 'all';
        // Reset the window — otherwise switching tabs leaves the old count
        // active and the user sees fewer rows than expected on the new filter.
        $this->txShown = self::PAGE_SIZE;
    }

    /**
     * Grow the window by PAGE_SIZE. Called from the view when the user
     * scrolls near the bottom of the list; no-op once we've loaded every
     * row (the view hides the sentinel in that case).
     */
    public function loadMoreTransactions(): void
    {
        $this->txShown += self::PAGE_SIZE;
    }

    /**
     * Reset the window whenever Livewire property-sync kicks in for txFilter
     * (clicking "All" / "Credits in" / "Credits out" via $set). The explicit
     * setFilter() method above handles the common case, but defensive — in
     * case any view uses wire:model.live on the filter.
     */
    public function updatedTxFilter(): void
    {
        $this->txShown = self::PAGE_SIZE;
    }

    public function render()
    {
        $user = Auth::user();

        // Same classification used by PurchaseCredits / CreditsHistory. Kept
        // locally rather than extracted — presentation-only, three callsites.
        $creditTypes = ['credit', 'credit_purchase'];
        $debitTypes  = ['debit', 'package_purchase', 'package_upgrade', 'paypal_payment', 'primary_gateway_payment'];

        // Query the full count first (cheap COUNT), then fetch up to $txShown
        // rows. The count tells the view whether to render the "load more"
        // sentinel; without it the IntersectionObserver would keep firing
        // forever at the bottom of a list that has no more rows to return.
        $base = $user
            ? WalletTransaction::query()
                ->where('user_id', $user->id)
                ->when($this->txFilter === 'credit', fn ($q) => $q->whereIn('type', $creditTypes))
                ->when($this->txFilter === 'debit',  fn ($q) => $q->whereIn('type', $debitTypes))
            : null;

        $txTotal = $base ? (clone $base)->count() : 0;
        $transactions = $base
            ? $base->orderByDesc('created_at')->limit($this->txShown)->get()
            : collect();
        $hasMoreTx = $transactions->count() < $txTotal;

        return view('livewire.wallet', [
            'balance'      => (float) (optional($user?->wallet)->balance ?? 0),
            'transactions' => $transactions,
            'txTotal'      => $txTotal,
            'hasMoreTx'    => $hasMoreTx,
            'creditTypes'  => $creditTypes,
            'debitTypes'   => $debitTypes,
        ]);
    }
}
