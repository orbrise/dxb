<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('components.layouts.app-evoory')]
class PurchaseCredits extends Component
{
    public $amount = 10;

    /**
     * Users who signed in via Google (or any other flow that skipped wallet
     * creation) reach this page without a `wallets` row, and the Blade view
     * dereferences `wallet->balance`, which fatals on null. Create one up
     * front so the render, the JS, and the payment callbacks all see a real
     * wallet from the first request.
     */
    public function mount()
    {
        $user = Auth::user();
        if ($user && !$user->wallet) {
            $user->wallet()->create(['balance' => 0]);
            // Refresh the relationship so subsequent $user->wallet reads in
            // this request return the newly-created row instead of null.
            $user->load('wallet');
        }
    }

    protected $rules = [
        'amount' => 'required|numeric|min:5',
    ];

    protected $messages = [
        'amount.required' => 'Amount is required.',
        'amount.numeric' => 'Amount must be a number.',
        'amount.min' => 'Minimum amount is $5.',
    ];
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function render()
    {
        return view('livewire.purchase-credits');
    }
    
    #[On('startPrimaryPaymentAttempt')]
    public function startPrimaryPaymentAttempt($amount, $referenceId)
    {
        $user = Auth::user();

        // If a row already exists for this reference (retry, or race with a
        // fast success/failure), don't overwrite its terminal state with pending.
        if ($referenceId && WalletTransaction::where('reference', $referenceId)->exists()) {
            return;
        }

        WalletTransaction::create([
            'user_id' => $user ? $user->id : null,
            'wallet_id' => $user && $user->wallet ? $user->wallet->id : null,
            'amount' => $amount,
            'type' => 'credit_purchase',
            'status' => 'pending',
            'payment_method' => 'primary_gateway',
            'description' => 'Credit purchase attempt started on primary gateway',
            'reference' => $referenceId,
        ]);
    }

    #[On('handlePayPalApproval')]
    public function handlePayPalApproval($orderId)
    {
        $this->validate();

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $user->wallet()->create(['balance' => 0]);
        }

        // Idempotency: if a completed row already exists for this order, do not
        // increment the wallet balance a second time.
        $existing = WalletTransaction::where('reference', $orderId)->first();
        if ($existing && $existing->status === 'completed') {
            return;
        }

        $wallet->increment('balance', $this->amount);

        WalletTransaction::updateOrCreate(
            ['reference' => $orderId],
            [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $this->amount,
                'type' => 'credit_purchase',
                'status' => 'completed',
                'payment_method' => 'paypal',
                'description' => 'PayPal payment for credits purchase',
                'error_code' => null,
                'decline_code' => null,
                'error_message' => null,
            ]
        );

        $this->dispatch('showMessage', [
            'type' => 'success',
            'message' => $this->amount . ' credits have been added to your account'
        ]);
    }

    #[On('processPrimaryPayment')]
    public function processPrimaryPayment($amount, $referenceId)
    {
        if ($amount < 5) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Invalid amount. Minimum amount is $5.'
            ]);
            return;
        }

        $user = Auth::user();
        $wallet = $user->wallet;

        if (!$wallet) {
            $wallet = $user->wallet()->create(['balance' => 0]);
        }

        // Idempotency: only credit the wallet if this reference hasn't already
        // been settled. A pending row is fine to promote to completed; a
        // completed row means we've already paid the user.
        $existing = WalletTransaction::where('reference', $referenceId)->first();
        if ($existing && $existing->status === 'completed') {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'This payment has already been processed.'
            ]);
            return;
        }

        $wallet->increment('balance', $amount);

        WalletTransaction::updateOrCreate(
            ['reference' => $referenceId],
            [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'amount' => $amount,
                'type' => 'credit_purchase',
                'status' => 'completed',
                'payment_method' => 'primary_gateway',
                'description' => 'Primary gateway payment for credits purchase',
                'error_code' => null,
                'decline_code' => null,
                'error_message' => null,
            ]
        );

        $this->dispatch('showMessage', [
            'type' => 'success',
            'message' => $amount . ' credits have been added to your account'
        ]);
    }

    #[On('handlePayPalFailure')]
    public function handlePayPalFailure($amount, $orderId = null, $reason = null, $errorMessage = null)
    {
        $user = Auth::user();

        // Never demote a completed payment
        $existing = $orderId ? WalletTransaction::where('reference', $orderId)->first() : null;
        if ($existing && $existing->status === 'completed') {
            return;
        }

        $lookup = $orderId
            ? ['reference' => $orderId]
            : ['reference' => 'PAYPAL_ERR_' . uniqid()];

        WalletTransaction::updateOrCreate(
            $lookup,
            [
                'user_id' => $user ? $user->id : null,
                'wallet_id' => $user && $user->wallet ? $user->wallet->id : null,
                'amount' => $amount,
                'type' => 'credit_purchase',
                'status' => $reason === 'cancelled' ? 'cancelled' : 'failed',
                'payment_method' => 'paypal',
                'description' => 'Credit purchase ' . ($reason === 'cancelled' ? 'cancelled' : 'failed') . ' on PayPal',
                'error_code' => $reason,
                'error_message' => $errorMessage,
            ]
        );
    }

    #[On('processPrimaryPaymentFailure')]
    public function processPrimaryPaymentFailure($amount, $referenceId, $errorCode = null, $declineCode = null, $errorMessage = null)
    {
        $user = Auth::user();

        // Never demote a completed payment
        $existing = $referenceId ? WalletTransaction::where('reference', $referenceId)->first() : null;
        if ($existing && $existing->status === 'completed') {
            return;
        }

        WalletTransaction::updateOrCreate(
            ['reference' => $referenceId],
            [
                'user_id' => $user ? $user->id : null,
                'wallet_id' => $user && $user->wallet ? $user->wallet->id : null,
                'amount' => $amount,
                'type' => 'credit_purchase',
                'status' => 'failed',
                'payment_method' => 'primary_gateway',
                'description' => 'Credit purchase failed on primary gateway',
                'error_code' => $errorCode,
                'decline_code' => $declineCode,
                'error_message' => $errorMessage,
            ]
        );

        $this->dispatch('showMessage', [
            'type' => 'error',
            'message' => $errorMessage ?: 'Payment failed. Please try again or use a different card.'
        ]);
    }
}
