<?php

namespace App\Livewire;

use App\Models\PremiumPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class PremiumCardPayment extends Component
{
    public string $plan = '1m';
    public string $gateway = 'secondary';

    public string $cardName = '';
    public string $cardNumber = '';
    public string $expiry = '';
    public string $cvc = '';

    public function mount(string $plan = '1m', string $gateway = 'secondary'): void
    {
        $this->plan = $plan;
        $this->gateway = in_array($gateway, ['primary', 'secondary'], true) ? $gateway : 'secondary';
        $this->cardName = Auth::user()?->name ?? '';
    }

    public function pay()
    {
        // TODO: run actual gateway charge via Stripe / chosen processor,
        // activate the subscription on the user, record transaction, etc.
        return redirect()->route('user.account.premium.success', ['plan' => $this->plan]);
    }

    public function render()
    {
        $planModel = PremiumPlan::where('slug', $this->plan)->where('is_active', true)->first();
        if (!$planModel) {
            abort(404, 'Plan not found');
        }

        return view('livewire.premium-card-payment', [
            'price'    => (float) $planModel->price,
            'duration' => $planModel->duration_days > 0
                            ? 'Evoory ' . $planModel->name . ' Membership'
                            : $planModel->name,
            'period'   => $planModel->period_label,
        ]);
    }
}
