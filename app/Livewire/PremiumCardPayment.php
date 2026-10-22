<?php

namespace App\Livewire;

use App\Models\PremiumPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class PremiumCardPayment extends Component
{
    public string $plan = '1m';
    public string $gateway = 'secondary';

    public function mount(string $plan = '1m', string $gateway = 'secondary'): void
    {
        $this->plan = $plan;
        $this->gateway = in_array($gateway, ['primary', 'secondary'], true) ? $gateway : 'secondary';
    }

    /**
     * Called from the iframe postMessage listener once myads reports
     * payment_success. Reference prefix (PREMIUM_) distinguishes this from
     * the credits / profile-upgrade flows that share the same relay page.
     * Activates the plan on the user (sets premium_plan_id + expiry) before
     * handing off to the success page — otherwise the success page / My
     * Account hero has nothing to render.
     */
    #[On('processPremiumPayment')]
    public function processPremiumPayment($referenceId = null)
    {
        $planModel = PremiumPlan::where('slug', $this->plan)->where('is_active', true)->first();
        $user = Auth::user();
        if ($planModel && $user) {
            $user->activatePremium($planModel, 'card', $referenceId);
        }
        return redirect()->route('user.account.premium.success', ['plan' => $this->plan]);
    }

    public function render()
    {
        $planModel = PremiumPlan::where('slug', $this->plan)->where('is_active', true)->first();
        if (!$planModel) {
            abort(404, 'Plan not found');
        }

        return view('livewire.premium-card-payment', [
            'price'       => (float) $planModel->price,
            'planName'    => $planModel->name,
            'duration'    => $planModel->duration_days > 0
                                ? 'Evoory ' . $planModel->name . ' Premium Membership'
                                : $planModel->name,
            'period'      => $planModel->period_label,
        ]);
    }
}
