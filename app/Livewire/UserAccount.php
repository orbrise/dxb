<?php

namespace App\Livewire;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Conversation;
use App\Support\ChatAllowance;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app-evoory')]
class UserAccount extends Component
{
    public function render()
    {
        // Counts + balance for the mobile account dashboard. Done in render()
        // so they refresh on every component update (favorites change, chat
        // count, wallet top-up, etc.) without a manual mount/event wire-up.
        $user = Auth::user();
        $profilesCount = $user ? $user->profiles()->count() : 0;
        $favoritesCount = $user ? $user->favorites()->count() : 0;
        $chatsCount = $user
            ? Conversation::where('user_one_id', $user->id)
                ->orWhere('user_two_id', $user->id)
                ->count()
            : 0;
        $walletBalance = ($user && $user->wallet) ? (float) $user->wallet->balance : 0;

        // Premium hero data — only populated when the user actually has an
        // active subscription. The view renders nothing when $premiumPlan
        // is null, so free users don't see the hero at all.
        $premiumPlan = null;
        $premiumDaysRemaining = 0;
        $premiumExpiresAt = null;
        if ($user && $user->hasActivePremium()) {
            $premiumPlan = $user->premiumPlan;
            $premiumExpiresAt = $user->premium_expires_at;
            $premiumDaysRemaining = max(0, (int) ceil(now()->diffInHours($premiumExpiresAt, false) / 24));
        }

        // "Call Now" in the support card rings every admin; tying it to the
        // user's Support conversation (my-chat creates the same one on load)
        // puts the call in the admin inbox's Calls tab and the chat history.
        $supportConversationId = $user ? Conversation::getOrCreateSupport($user->id)->id : null;

        return view('livewire.user-account', [
            // The free plan's people limit (ChatAllowance) for the plan card.
            'chatLimited' => $user ? ChatAllowance::isLimited($user) : true,
            // Staff aren't limited either, so "not limited" doesn't mean Premium.
            'hasPremium' => $user && $user->hasActivePremium(),
            'chatPeopleUsed' => $user ? ChatAllowance::used($user) : 0,
            'supportConversationId' => $supportConversationId,
            'profilesCount' => $profilesCount,
            'favoritesCount' => $favoritesCount,
            'chatsCount' => $chatsCount,
            'walletBalance' => $walletBalance,
            'premiumPlan' => $premiumPlan,
            'premiumDaysRemaining' => $premiumDaysRemaining,
            'premiumExpiresAt' => $premiumExpiresAt,
        ]);
    }
}
