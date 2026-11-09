<?php

namespace App\Livewire;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app-evoory')]
class HelpCenter extends Component
{
    /**
     * Help page is mobile-only. Desktop visitors are funneled straight to
     * the existing sign-in page. Mobile detection is a User-Agent regex —
     * covers iOS, Android, Windows Phone, BlackBerry, and Opera Mini. Not
     * 100% bulletproof (no UA detection ever is), but matches the common
     * `is_mobile()` helpers found in Laravel projects.
     */
    public function mount(Request $request)
    {
        if (!$this->isMobile($request->userAgent() ?? '')) {
            return redirect()->route('sign-in');
        }
    }

    public function render()
    {
        // Logged in: the "Direct Call on evoory" card calls Support on the
        // user's Support conversation (as on My Account). Guests get null
        // and the card's Call Now runs loginToCall().
        return view('livewire.help-center', [
            'supportConversationId' => auth()->check()
                ? Conversation::getOrCreateSupport(auth()->id())->id
                : null,
        ]);
    }

    /**
     * Website calls need an account: send a guest to Log In, and back here
     * afterwards (LoginController redirects to the intended URL), where Call
     * Now starts the support call.
     */
    public function loginToCall()
    {
        session()->put('url.intended', route('help'));

        return $this->redirect(route('sign-in'), navigate: true);
    }

    // Guest tapped Message on the "Direct on evoory" card: after signing in,
    // land straight in their Support chat.
    public function loginToChat()
    {
        session()->put('url.intended', route('user.chat', ['support' => 1]));

        return $this->redirect(route('sign-in'), navigate: true);
    }

    protected function isMobile(string $userAgent): bool
    {
        return (bool) preg_match(
            '/(Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|webOS|Windows Phone)/i',
            $userAgent
        );
    }
}
