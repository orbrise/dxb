<?php

namespace App\Livewire;

use App\Models\PremiumPlan;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class PremiumSuccess extends Component
{
    public string $plan = '1m';

    public function mount(string $plan = '1m'): void
    {
        $this->plan = $plan;
    }

    public function render()
    {
        $planModel = PremiumPlan::where('slug', $this->plan)->first();
        if (!$planModel) {
            abort(404, 'Plan not found');
        }

        return view('livewire.premium-success', [
            'planLabel' => $planModel->name,
            'planPrice' => (float) $planModel->price,
        ]);
    }
}
