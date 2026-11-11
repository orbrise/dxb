<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Support\ChatTyping;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * The "typing…" bubble in /my-chat and the admin Support Inbox
 * (see App\Support\ChatTyping). Both endpoints are hit every couple of
 * seconds while a chat is open, so they stay to one small query each.
 */
class ChatTypingController extends Controller
{
    /** POST /chat/typing/{convId} — typing=1 while typing, typing=0 when the box is emptied. */
    public function ping(Request $request, int $convId): JsonResponse
    {
        $conversation = $this->conversation($convId);
        $side = $conversation ? ChatTyping::sideOf($conversation, $request->user()) : null;
        if ($side === null) return response()->json(['ok' => false], 403);

        $request->boolean('typing')
            ? ChatTyping::start($conversation->id, $side)
            : ChatTyping::stop($conversation->id, $side);

        return response()->json(['ok' => true])->header('Cache-Control', 'no-store, private');
    }

    /** GET /chat/typing/{convId} — is the other side typing right now? */
    public function status(Request $request, int $convId): JsonResponse
    {
        $conversation = $this->conversation($convId);
        if (!$conversation || ChatTyping::sideOf($conversation, $request->user()) === null) {
            return response()->json(['ok' => false], 403);
        }

        return response()->json([
            'ok' => true,
            'typing' => ChatTyping::isPeerTyping($conversation, $request->user()),
        ])->header('Cache-Control', 'no-store, private');
    }

    protected function conversation(int $convId): ?Conversation
    {
        return Conversation::query()->find($convId, ['id', 'user_one_id', 'user_two_id', 'is_support']);
    }
}
