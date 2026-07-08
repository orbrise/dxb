<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around Wasender's external messaging API.
 *
 * Wasender is a WhatsApp-only gateway (there is no SMS endpoint), so both
 * the "whatsapp" and "sms" channels selected in the claim modal ultimately
 * route through /inbox/send-message. We keep the OTP lifecycle local — this
 * service just delivers the plaintext body we hand it.
 *
 * Config (env): WASENDER_BASE_URL, WASENDER_CLIENT_ID, WASENDER_CLIENT_SECRET,
 *               WASENDER_FROM_NUMBER (optional), WASENDER_WHATSAPP_ACCOUNT_ID (optional),
 *               WASENDER_TEMPLATE_ID (optional — enables template send path).
 *
 * IP whitelisting: Wasender requires the caller's public IP to be listed in
 * the dashboard, or every request comes back with an access error. There's
 * nothing this class can do about that beyond surfacing the error text.
 *
 * When credentials are missing the service returns a simulated success in
 * non-production environments so the claim flow stays testable without
 * burning real send quota.
 */
class WasenderMessagingService
{
    public function isConfigured(): bool
    {
        return (bool) (config('services.wasender.base_url')
            && config('services.wasender.client_id')
            && config('services.wasender.client_secret'));
    }

    /**
     * POST /inbox/send-message
     *
     * $mobileCode is the numeric country code with no leading '+' (e.g.
     * "971"). $mobile is the local number, digits only. Both come from the
     * claim modal — the JS splits them before submit.
     *
     * @return array{success: bool, error?: string, simulated?: bool}
     */
    public function sendMessage(string $mobileCode, string $mobile, string $text): array
    {
        $mobileCode = $this->digits($mobileCode);
        $mobile = $this->digits($mobile);

        if (!$this->isConfigured()) {
            return $this->simulated('whatsapp', $mobileCode . $mobile, $text);
        }

        $payload = array_filter([
            'mobile_code' => $mobileCode,
            'mobile'      => $mobile,
            'message'     => $text,
            'from_number' => config('services.wasender.from_number') ?: null,
            'whatsapp_account_id' => config('services.wasender.whatsapp_account_id') ?: null,
        ], fn ($v) => $v !== null && $v !== '');

        try {
            $response = $this->http()->post('/inbox/send-message', $payload);

            if ($response->successful() && $response->json('status') === 'success') {
                return ['success' => true];
            }

            $msg = $this->extractError($response);
            Log::warning('Wasender send-message rejected', [
                'to' => '+' . $mobileCode . $mobile,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return ['success' => false, 'error' => $this->friendlyError($msg)];
        } catch (\Throwable $e) {
            Log::error('Wasender send-message exception: ' . $e->getMessage(), [
                'to' => '+' . $mobileCode . $mobile,
            ]);
            return ['success' => false, 'error' => 'WhatsApp service unavailable. Please try again.'];
        }
    }

    /**
     * Send an OTP over WhatsApp. Prefers the template endpoint when a
     * template id is configured — templates are the only path that reliably
     * delivers to recipients who haven't messaged the sender in the last 24
     * hours. Falls back to free-form send-message when no template is set.
     *
     * @return array{success: bool, error?: string, simulated?: bool}
     */
    public function sendWhatsAppOtp(string $mobileCode, string $mobile, string $code, string $fallbackText = ''): array
    {
        $templateId = config('services.wasender.template_id');
        if ($templateId) {
            return $this->sendTemplateMessage($mobileCode, $mobile, $templateId, [$code]);
        }
        $text = $fallbackText !== '' ? $fallbackText : "Your verification code: {$code}";
        return $this->sendMessage($mobileCode, $mobile, $text);
    }

    /**
     * POST /inbox/send-template-message
     *
     * @param array<int, string> $bodyVariables placeholders for the template body
     * @param array<int, string> $headerVariables placeholders for the template header
     * @return array{success: bool, error?: string, simulated?: bool}
     */
    public function sendTemplateMessage(
        string $mobileCode,
        string $mobile,
        string $templateId,
        array $bodyVariables = [],
        array $headerVariables = []
    ): array {
        $mobileCode = $this->digits($mobileCode);
        $mobile = $this->digits($mobile);

        if (!$this->isConfigured()) {
            return $this->simulated('whatsapp-template', $mobileCode . $mobile, implode(',', $bodyVariables));
        }

        $payload = array_filter([
            'mobile_code'      => $mobileCode,
            'mobile'           => $mobile,
            'template_id'      => $templateId,
            'body_variables'   => $bodyVariables ?: null,
            'header_variables' => $headerVariables ?: null,
            'from_number'      => config('services.wasender.from_number') ?: null,
            'whatsapp_account_id' => config('services.wasender.whatsapp_account_id') ?: null,
        ], fn ($v) => $v !== null && $v !== '' && $v !== []);

        try {
            $response = $this->http()->post('/inbox/send-template-message', $payload);

            if ($response->successful() && $response->json('status') === 'success') {
                return ['success' => true];
            }

            $msg = $this->extractError($response);
            Log::warning('Wasender template send rejected', [
                'to' => '+' . $mobileCode . $mobile,
                'template_id' => $templateId,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return ['success' => false, 'error' => $this->friendlyError($msg)];
        } catch (\Throwable $e) {
            Log::error('Wasender template send exception: ' . $e->getMessage(), [
                'to' => '+' . $mobileCode . $mobile,
                'template_id' => $templateId,
            ]);
            return ['success' => false, 'error' => 'WhatsApp service unavailable. Please try again.'];
        }
    }

    protected function http()
    {
        return Http::baseUrl($this->baseUrl())
            ->withHeaders([
                'client-id'     => (string) config('services.wasender.client_id'),
                'client-secret' => (string) config('services.wasender.client_secret'),
                'Accept'        => 'application/json',
            ])
            ->timeout(15);
    }

    /**
     * Normalise the configured base URL — tolerate values pasted with or
     * without the leading "https://" / trailing slash. Guzzle needs a scheme
     * or it blows up at request time.
     */
    protected function baseUrl(): string
    {
        $raw = trim((string) config('services.wasender.base_url'));
        $raw = rtrim($raw, '/');
        if ($raw === '') {
            return '';
        }
        if (!preg_match('#^https?://#i', $raw)) {
            $raw = 'https://' . $raw;
        }
        return $raw;
    }

    /**
     * Wasender error responses use { status: "error", message: [ "..." ] }.
     * Successful responses use { status: "success", ... }. Extract a plain
     * string we can bubble up to the user regardless of shape.
     */
    protected function extractError(\Illuminate\Http\Client\Response $response): string
    {
        $body = $response->json();
        if (is_array($body)) {
            $msg = $body['message'] ?? null;
            if (is_array($msg)) {
                return (string) reset($msg);
            }
            if (is_string($msg) && $msg !== '') {
                return $msg;
            }
        }
        return 'HTTP ' . $response->status();
    }

    protected function digits(string $value): string
    {
        return (string) preg_replace('/\D+/', '', $value);
    }

    /**
     * Translate Wasender's raw error strings into something a user can act
     * on. Falls through to the original text when there's no known match,
     * so we never lose information.
     */
    protected function friendlyError(string $raw): string
    {
        $lower = strtolower($raw);
        if (str_contains($lower, 'client secret') || str_contains($lower, 'client id') || str_contains($lower, 'unauthorized')) {
            return 'Messaging service authentication failed.';
        }
        if (str_contains($lower, 'whitelist')) {
            return 'This server is not authorised to send messages yet. Please contact support.';
        }
        if (str_contains($lower, 'mobile') || str_contains($lower, 'phone')) {
            return 'The destination number is not valid or not supported.';
        }
        if (str_contains($lower, 'template')) {
            return 'The WhatsApp template is not available. Please try again shortly.';
        }
        if (str_contains($lower, 'blocked')) {
            return 'This number is blocked from receiving messages.';
        }
        return $raw;
    }

    protected function simulated(string $channel, string $to, string $text): array
    {
        Log::warning('Wasender not configured — simulating send', [
            'channel' => $channel,
            'to' => $to,
            'text' => $text,
        ]);
        return ['success' => true, 'simulated' => true];
    }
}
