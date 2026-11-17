<?php

namespace App\Http\Controllers;

use App\Support\SupportChat;
use Illuminate\Http\Request;

/**
 * Buy Credits → Contact Support → "Message Support". A plain form POST
 * rather than a Livewire event: on production the page script that used to
 * drive this button didn't run, so the click did nothing. A form needs no
 * page script, no Livewire round-trip and nothing Rocket Loader can defer.
 */
class CreditsSupportController extends Controller
{
    public function __invoke(Request $request)
    {
        $amount = $request->input('amount');

        if (!is_numeric($amount) || $amount < 10) {
            return redirect()->route('purchase.credits')->with(
                'credits_support_error',
                is_numeric($amount) && $amount > 0 ? 'Minimum amount is $10.' : 'Enter the amount you want to add first.'
            );
        }

        $amount = (float) $amount;
        $shown = $amount == floor($amount) ? (int) $amount : number_format($amount, 2, '.', '');

        SupportChat::send($request->user()->id, "Hi, I'd like to add \${$shown} to my wallet. How can I pay?");

        return redirect()->route('user.chat', ['support' => 1]);
    }
}
