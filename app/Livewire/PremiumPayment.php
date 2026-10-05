<?php

namespace App\Livewire;

use App\Models\PremiumPlan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class PremiumPayment extends Component
{
    public string $plan = '1m';
    public string $gateway = 'secondary';

    public function mount(string $plan = '1m'): void
    {
        $this->plan = $plan;
    }

    public function selectGateway(string $gateway): void
    {
        if (in_array($gateway, ['primary', 'secondary'], true)) {
            $this->gateway = $gateway;
        }
    }

    public function render()
    {
        $planModel = PremiumPlan::where('slug', $this->plan)->where('is_active', true)->first();
        if (!$planModel) {
            abort(404, 'Plan not found');
        }

        return view('livewire.premium-payment', [
            'price'    => (float) $planModel->price,
            'duration' => $planModel->duration_days > 0
                            ? $planModel->duration_days . ' Days Premium Membership'
                            : $planModel->name,
            'period'   => $planModel->period_label,
        ]);
    }
}
