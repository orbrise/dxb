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
        // Wallet → straight to success. TODO: deduct from wallet and
        // activate the subscription in a transaction before redirecting.
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
