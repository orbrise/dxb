<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class PurchaseCredits extends Component
{
    public $amount = 100;
    
    protected $rules = [
        'amount' => 'required|numeric|min:10|max:100',
    ];
    
    protected $messages = [
        'amount.required' => 'Amount is required.',
        'amount.numeric' => 'Amount must be a number.',
        'amount.min' => 'Minimum amount is $10.',
        'amount.max' => 'Maximum amount is $100.',
    ];
    
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function render()
    {
        return view('livewire.purchase-credits');
    }
    
    #[On('handlePayPalApproval')]
    public function handlePayPalApproval($orderId)
    {
        // Validate amount before processing
        $this->validate();
        
        // Get user wallet
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            // Create wallet if it doesn't exist
            $wallet = $user->wallet()->create([
                'balance' => 0
            ]);
        }
        
        // Add credits to wallet
        $wallet->increment('balance', $this->amount);
        
        // Create transaction record
        WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $this->amount,
            'type' => 'credit_purchase',
            'status' => 'completed',
            'description' => 'PayPal payment for credits purchase',
            'reference' => $orderId
        ]);
        
        // Send success message
        $this->dispatch('showMessage', [
            'type' => 'success',
            'message' => $this->amount . ' credits have been added to your account'
        ]);
    }
    
    #[On('processPrimaryPayment')]
    public function processPrimaryPayment($amount, $referenceId)
    {
        // Validate amount
        if ($amount < 10 || $amount > 100) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'Invalid amount. Please enter an amount between $10 and $100.'
            ]);
            return;
        }
        
        // Check if this reference has already been processed
        $existingTransaction = WalletTransaction::where('reference', $referenceId)->first();
        if ($existingTransaction) {
            $this->dispatch('showMessage', [
                'type' => 'error',
                'message' => 'This payment has already been processed.'
            ]);
            return;
        }
        
        // Get user wallet
        $user = Auth::user();
        $wallet = $user->wallet;
        
        if (!$wallet) {
            // Create wallet if it doesn't exist
            $wallet = $user->wallet()->create([
                'balance' => 0
            ]);
        }
        
        // Add credits to wallet
        $wallet->increment('balance', $amount);
        
        // Create transaction record
        WalletTransaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'credit_purchase',
            'status' => 'completed',
            'description' => 'Primary gateway payment for credits purchase',
            'reference' => $referenceId
        ]);
        
        // Send success message
        $this->dispatch('showMessage', [
            'type' => 'success',
            'message' => $amount . ' credits have been added to your account'
        ]);
    }
}
