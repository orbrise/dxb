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
    }

    public function render()
    {
        $user = Auth::user();

        // Same classification used by PurchaseCredits / CreditsHistory. Kept
        // locally rather than extracted — presentation-only, three callsites.
        $creditTypes = ['credit', 'credit_purchase'];
        $debitTypes  = ['debit', 'package_purchase', 'package_upgrade', 'paypal_payment', 'primary_gateway_payment'];

        $transactions = $user
            ? WalletTransaction::query()
                ->where('user_id', $user->id)
                ->when($this->txFilter === 'credit', fn ($q) => $q->whereIn('type', $creditTypes))
                ->when($this->txFilter === 'debit',  fn ($q) => $q->whereIn('type', $debitTypes))
                ->orderByDesc('created_at')
                ->limit(50)
                ->get()
            : collect();

        return view('livewire.wallet', [
            'balance'      => (float) (optional($user?->wallet)->balance ?? 0),
            'transactions' => $transactions,
            'creditTypes'  => $creditTypes,
            'debitTypes'   => $debitTypes,
        ]);
    }
}
