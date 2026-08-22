<?php

namespace App\Livewire;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app-evoory')]
class CreditsHistory extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public string $filter = 'all';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Types classified as money-in vs money-out. Kept here (not on the model) because
        // the classification is presentation logic — the DB stores type strings only.
        $creditTypes = ['credit', 'credit_purchase'];
        $debitTypes = ['debit', 'package_purchase', 'package_upgrade', 'paypal_payment', 'primary_gateway_payment'];

        $query = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->with('package:id,name')
            ->when($this->filter === 'credit', fn($q) => $q->whereIn('type', $creditTypes))
            ->when($this->filter === 'debit', fn($q) => $q->whereIn('type', $debitTypes))
            ->orderByDesc('created_at');

        $transactions = $query->paginate(25);

        $totals = WalletTransaction::query()
            ->where('user_id', $user->id)
            ->selectRaw("
                SUM(CASE WHEN type IN ('credit','credit_purchase') THEN amount ELSE 0 END) as total_loaded,
                SUM(CASE WHEN type IN ('debit','package_purchase','package_upgrade','paypal_payment','primary_gateway_payment') THEN amount ELSE 0 END) as total_spent
            ")
            ->first();

        return view('livewire.credits-history', [
            'transactions' => $transactions,
            'walletBalance' => $user->wallet->balance ?? 0,
            'totalLoaded' => $totals->total_loaded ?? 0,
            'totalSpent' => $totals->total_spent ?? 0,
            'creditTypes' => $creditTypes,
            'debitTypes' => $debitTypes,
        ]);
    }
}
