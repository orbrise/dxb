<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\SupportBot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin wrapper around xAI's OpenAI-compatible chat/completions endpoint.
 * One public entry point: draftReply() — hands back a plain-text draft
 * the admin can edit before sending.
 */
class GrokSupportBot
{
    public function isConfigured(): bool
    {
        return !empty(config('services.grok.api_key'));
    }

    public function draftReply(SupportBot $bot, Conversation $conversation): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Grok API key is not configured.');
        }

        $messages = $this->buildMessages($bot, $conversation);
        return $this->callGrok($bot->model ?: config('services.grok.model'), $messages);
    }

    /**
     * Test-prompt entry — admin types a sample customer message in the Bots
     * management page and this returns what the bot would reply, with no
     * conversation history. Lets admins iterate on system_prompt + knowledge
     * without needing a real inbound message.
     */
    public function test(SupportBot $bot, string $customerMessage): string
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('Grok API key is not configured.');
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemBlock($bot)],
            ['role' => 'user', 'content' => $customerMessage],
        ];

        return $this->callGrok($bot->model ?: config('services.grok.model'), $messages);
    }

    private function buildMessages(SupportBot $bot, Conversation $conversation): array
    {
        $recent = Message::where('conversation_id', $conversation->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->sortBy('created_at')
            ->values();

        $customerId = $conversation->user_one_id;

        $history = [];
        foreach ($recent as $m) {
            // Attachment-only rows become a short placeholder so the model
            // knows something was sent without us needing to download it.
            $content = trim((string) $m->message);
            if ($content === '' && !empty($m->attachment_type)) {
                $content = '[' . $m->attachment_type . ' attachment]';
            }
            if ($content === '') continue;

            $history[] = [
                'role'    => $m->sender_id === $customerId ? 'user' : 'assistant',
                'content' => $content,
            ];
        }

        return array_merge(
            [['role' => 'system', 'content' => $this->systemBlock($bot)]],
            $history
        );
    }

    private function systemBlock(SupportBot $bot): string
    {
        $parts = [
            trim($bot->system_prompt ?: ''),
        ];

        if (!empty($bot->knowledge)) {
            $parts[] = "## Knowledge base\n" . trim($bot->knowledge);
        }

        $parts[] = "## Rules\n"
            . "- Keep replies concise and polite.\n"
            . "- Never invent prices, refund amounts, or policies that are not in the knowledge base.\n"
            . "- If the customer asks something outside the knowledge base, say you'll get back to them.\n"
            . "- Never reveal that you are an AI. Reply as the admin would.";

        $full = implode("\n\n", array_filter($parts));
        $max  = (int) config('services.grok.max_prompt_chars', 24000);
        return mb_substr($full, 0, $max);
    }

    private function callGrok(string $model, array $messages): string
    {
        $base = rtrim((string) config('services.grok.base_url'), '/');
        $key  = (string) config('services.grok.api_key');

        $response = Http::withToken($key)
            ->timeout((int) config('services.grok.request_timeout', 30))
            ->acceptJson()
            ->post($base . '/chat/completions', [
                'model'       => $model,
                'messages'    => $messages,
                'temperature' => 0.4,
                'max_tokens'  => (int) config('services.grok.max_output_tokens', 600),
            ]);

        if (!$response->successful()) {
            Log::warning('grok draftReply failed', [
                'status' => $response->status(),
                'body'   => mb_substr($response->body(), 0, 500),
            ]);
            throw new RuntimeException('Grok API returned ' . $response->status() . '.');
        }

        $data = $response->json();
        $text = $data['choices'][0]['message']['content'] ?? '';
        $text = trim((string) $text);

        if ($text === '') {
            throw new RuntimeException('Grok returned an empty reply.');
        }

        return $text;
    }
}
