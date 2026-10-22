<?php

namespace App\Livewire;

use App\Models\PremiumPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class PremiumCheckout extends Component
{
    public string $plan = '1m';
    public string $paymentMethod = 'wallet';

    public function mount(string $plan = '1m'): void
    {
        $this->plan = $plan;
    }

    public function selectMethod(string $method): void
    {
        if (in_array($method, ['card', 'wallet'], true)) {
            $this->paymentMethod = $method;
        }
    }

    /**
     * Called from Alpine's Pay button with the currently-selected method.
     * Only hits the server once (at submit time) so the method toggle
     * stays snappy on slow connections.
     */
    public function proceed(?string $method = null)
    {
        $method = in_array($method, ['card', 'wallet'], true) ? $method : $this->paymentMethod;

        if ($method === 'card') {
            return redirect()->route('user.account.premium.payment', ['plan' => $this->plan]);
        }

        // Wallet path — deduct balance and activate the plan in one go so
        // the My Account hero + success page have state to display. Guard
        // against double-charge / insufficient balance here (not just at
        // Alpine level) because the client-side canUseWallet check is
        // trivially bypass-able.
        $planModel = PremiumPlan::where('slug', $this->plan)->where('is_active', true)->first();
        $user = Auth::user();
        if (!$planModel || !$user) {
            abort(404, 'Plan not found');
        }

        $price = (float) $planModel->price;
        $wallet = $user->wallet;
        if ($price > 0) {
            if (!$wallet || (float) $wallet->balance < $price) {
                session()->flash('error', 'Insufficient wallet balance.');
                return redirect()->route('user.account.premium.checkout', ['plan' => $this->plan]);
            }
            \DB::transaction(function () use ($wallet, $price, $user, $planModel) {
                $wallet->decrement('balance', $price);
                $user->activatePremium($planModel, 'wallet');
            });
        } else {
            $user->activatePremium($planModel, 'free');
        }

        return redirect()->route('user.account.premium.success', ['plan' => $this->plan]);
    }

    public function render()
    {
        $planModel = PremiumPlan::where('slug', $this->plan)->where('is_active', true)->first();
        if (!$planModel) {
            abort(404, 'Plan not found');
        }
        $price = (float) $planModel->price;
        $user = Auth::user();
        $walletBalance = ($user && $user->wallet) ? (float) $user->wallet->balance : 0.0;

        return view('livewire.premium-checkout', [
            'price'           => $price,
            'duration'        => $planModel->duration_days > 0
                                   ? $planModel->duration_days . ' Days Premium Membership'
                                   : $planModel->name,
            'period'          => $planModel->period_label,
            'walletBalance'   => $walletBalance,
            'remainingAfter'  => max(0, $walletBalance - $price),
            'canUseWallet'    => $walletBalance >= $price,
        ]);
    }
}
