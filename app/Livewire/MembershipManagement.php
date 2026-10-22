<?php

namespace App\Livewire;

use App\Models\PremiumTransaction;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app-evoory')]
class MembershipManagement extends Component
{
    public function render()
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $hasActive = $user->hasActivePremium();
        $plan      = $user->premiumPlan;
        $expiresAt = $user->premium_expires_at;

        $latestTx = PremiumTransaction::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->first();

        // Purchase date = created_at of the latest successful transaction.
        // Falls back to (expiry - plan duration) so a legacy upgrade without
        // a ledger row still renders a sensible date.
        $purchaseDate = $latestTx?->created_at;
        if (!$purchaseDate && $expiresAt && $plan) {
            $purchaseDate = $expiresAt->copy()->subDays(max(0, (int) $plan->duration_days));
        }

        $daysRemaining = 0;
        $durationPct   = 0;
        if ($hasActive && $expiresAt) {
            $daysRemaining = max(0, (int) ceil(now()->diffInHours($expiresAt, false) / 24));
            if ($purchaseDate && $expiresAt->greaterThan($purchaseDate)) {
                $totalHours   = $purchaseDate->diffInHours($expiresAt);
                $elapsedHours = max(0, min($totalHours, $purchaseDate->diffInHours(now())));
                $durationPct  = $totalHours > 0 ? (int) round(($elapsedHours / $totalHours) * 100) : 0;
            }
        }

        $paymentMethodLabel = match ($latestTx?->payment_method) {
            'wallet' => 'Evoory Wallet',
            'card'   => 'Credit / Debit Card',
            'free'   => 'Free Plan',
            default  => '—',
        };

        $history = PremiumTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('livewire.membership-management', [
            'hasActive'          => $hasActive,
            'plan'               => $plan,
            'planName'           => $plan?->name ?? ($latestTx?->plan_name_snapshot ?? '—'),
            'expiresAt'          => $expiresAt,
            'purchaseDate'       => $purchaseDate,
            'daysRemaining'      => $daysRemaining,
            'durationPct'        => $durationPct,
            'paymentMethodLabel' => $paymentMethodLabel,
            'history'            => $history,
        ]);
    }
}
