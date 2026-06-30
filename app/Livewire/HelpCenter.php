<?php

namespace App\Livewire;

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
        return view('livewire.help-center');
    }

    protected function isMobile(string $userAgent): bool
    {
        return (bool) preg_match(
            '/(Mobile|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|webOS|Windows Phone)/i',
            $userAgent
        );
    }
}
