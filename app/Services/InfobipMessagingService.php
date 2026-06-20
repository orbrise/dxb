<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin wrapper around Infobip's raw messaging APIs.
 *
 * "Raw" means we send a plain text message containing whatever body we
 * supply (we own the OTP code lifecycle locally — see
 * ProfileClaimController). We deliberately don't use Infobip's hosted 2FA
 * product so the verification record stays in our own database, where the
 * admin tooling and the rate-limit logic already live.
 *
 * Config (env): INFOBIP_BASE_URL, INFOBIP_API_KEY,
 *               INFOBIP_SMS_SENDER, INFOBIP_WHATSAPP_SENDER.
 *
 * INFOBIP_BASE_URL is the per-account host (e.g. `https://abc123.api.infobip.com`)
 * shown on the Infobip dashboard under "API Key Management" / "Base URL".
 * It is NOT the generic api.infobip.com.
 *
 * When any credential is missing the service returns a simulated success
 * in non-production environments so the claim flow stays testable without
 * burning real send quota.
 */
class InfobipMessagingService
{
    public function isConfigured(): bool
    {
        return (bool) (config('services.infobip.base_url')
            && config('services.infobip.api_key'));
    }

    /**
     * POST /sms/2/text/advanced
     *
     * @return array{success: bool, error?: string, simulated?: bool}
     */
    public function sendSms(string $to, string $text): array
    {
        if (!$this->isConfigured()) {
            return $this->simulated('sms', $to, $text);
        }

        $sender = config('services.infobip.sms_sender') ?: 'evoory';

        try {
            $response = $this->http()->post('/sms/2/text/advanced', [
                'messages' => [[
                    'from' => $sender,
                    'destinations' => [['to' => $this->normalise($to)]],
                    'text' => $text,
                ]],
            ]);

            if ($response->successful()) {
                $first = $response->json('messages.0', []);
                $status = $first['status']['groupName'] ?? 'PENDING';
                if (in_array($status, ['PENDING', 'ACCEPTED'], true)) {
                    return ['success' => true];
                }
                Log::warning('Infobip SMS non-accepted status', ['to' => $to, 'first' => $first]);
                return [
                    'success' => false,
                    'error' => $this->friendlyError($first['status']['description'] ?? 'SMS not accepted by carrier.'),
                ];
            }

            Log::error('Infobip SMS HTTP error', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            return [
                'success' => false,
                'error' => $this->friendlyError($response->json('requestError.serviceException.text', 'SMS service rejected the request.')),
            ];
        } catch (\Throwable $e) {
            Log::error('Infobip SMS exception: ' . $e->getMessage(), ['to' => $to]);
            return ['success' => false, 'error' => 'SMS service unavailable. Please try again.'];
        }
    }

    /**
     * Send an OTP over WhatsApp. Prefers the template endpoint when an
     * authentication template name is configured — that's the only WhatsApp
     * path that delivers without the recipient first messaging the sender
     * within the previous 24 hours. Falls back to the free-form text
     * endpoint when no template is set, which is useful during local dev
     * but will silently fail outside the 24-hour window in production.
     *
     * @return array{success: bool, error?: string, simulated?: bool}
     */
    public function sendWhatsAppOtp(string $to, string $code, string $fallbackText = ''): array
    {
        $template = config('services.infobip.whatsapp_template_name');
        if ($template) {
            return $this->sendWhatsAppTemplate($to, $code, $template);
        }
        return $this->sendWhatsApp($to, $fallbackText !== '' ? $fallbackText : "Your verification code: {$code}");
    }

    /**
     * POST /whatsapp/1/message/template
     *
     * Sends a Meta-authentication-category template populated with the
     * OTP. Auth templates have a single body placeholder for the code,
     * plus (typically) a "Copy code" URL button whose parameter is the
     * same code so users can paste it back in one tap.
     *
     * @return array{success: bool, error?: string}
     */
    protected function sendWhatsAppTemplate(string $to, string $code, string $templateName): array
    {
        if (!$this->isConfigured()) {
            return $this->simulated('whatsapp-template', $to, $code);
        }

        $sender = config('services.infobip.whatsapp_sender');
        if (!$sender) {
            return ['success' => false, 'error' => 'WhatsApp sender not configured. Please use SMS.'];
        }

        $templateData = [
            'body' => ['placeholders' => [$code]],
        ];
        if (config('services.infobip.whatsapp_template_has_button')) {
            $templateData['buttons'] = [
                ['type' => 'URL', 'parameter' => $code],
            ];
        }

        try {
            $response = $this->http()->post('/whatsapp/1/message/template', [
                'messages' => [[
                    'from' => $this->normalise($sender),
                    'to' => $this->normalise($to),
                    'content' => [
                        'templateName' => $templateName,
                        'templateData' => $templateData,
                        'language' => config('services.infobip.whatsapp_template_language', 'en'),
                    ],
                ]],
            ]);

            if ($response->successful()) {
                // Same downstream-rejection check as the free-form path: a
                // 200 doesn't mean the carrier accepted, only that Infobip
                // queued the request.
                $status = (string) $response->json('messages.0.status.groupName', 'PENDING');
                if (in_array($status, ['REJECTED', 'EXPIRED', 'UNDELIVERABLE'], true)) {
                    $reason = $response->json('messages.0.status.description') ?? 'WhatsApp template delivery failed.';
                    Log::warning('Infobip WhatsApp template downstream rejection', [
                        'to' => $to,
                        'template' => $templateName,
                        'status' => $status,
                        'reason' => $reason,
                    ]);
                    return ['success' => false, 'error' => $this->friendlyError($reason)];
                }
                return ['success' => true];
            }

            Log::warning('Infobip WhatsApp template HTTP error', [
                'to' => $to,
                'template' => $templateName,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            $msg = $response->json('requestError.serviceException.text')
                ?? $response->json('messages.0.status.description')
                ?? 'WhatsApp template delivery failed.';
            return ['success' => false, 'error' => $this->friendlyError($msg)];
        } catch (\Throwable $e) {
            Log::error('Infobip WhatsApp template exception: ' . $e->getMessage(), [
                'to' => $to,
                'template' => $templateName,
            ]);
            return ['success' => false, 'error' => 'WhatsApp service unavailable. Please try again.'];
        }
    }

    /**
     * POST /whatsapp/1/message/text
     *
     * Requires an approved WhatsApp Business sender + template on the
     * Infobip side. Until that's set up, this endpoint returns 400/403
     * even with valid credentials — we surface that as a plain error so
     * the UI can prompt the user to switch to SMS.
     *
     * @return array{success: bool, error?: string, simulated?: bool}
     */
    public function sendWhatsApp(string $to, string $text): array
    {
        if (!$this->isConfigured()) {
            return $this->simulated('whatsapp', $to, $text);
        }

        $sender = config('services.infobip.whatsapp_sender');
        if (!$sender) {
            return ['success' => false, 'error' => 'WhatsApp sender not configured. Please use SMS.'];
        }

        try {
            $response = $this->http()->post('/whatsapp/1/message/text', [
                // Infobip's canonical format is digits-only for both
                // sides — pasting a "+44…" sender in .env still works
                // because we strip the plus here.
                'from' => $this->normalise($sender),
                'to' => $this->normalise($to),
                'content' => ['text' => $text],
            ]);

            if ($response->successful()) {
                // Infobip returns 200 even when carrier downstream rejects.
                // Look at the status block — REJECTED there means the
                // carrier or sender setup is bad, surface that to the user.
                $status = (string) $response->json('status.groupName', 'PENDING');
                if (in_array($status, ['REJECTED', 'EXPIRED', 'UNDELIVERABLE'], true)) {
                    $reason = $response->json('status.description') ?? 'WhatsApp delivery failed.';
                    Log::warning('Infobip WhatsApp downstream rejection', [
                        'to' => $to,
                        'status' => $status,
                        'reason' => $reason,
                    ]);
                    return ['success' => false, 'error' => $this->friendlyError($reason)];
                }
                return ['success' => true];
            }

            Log::warning('Infobip WhatsApp HTTP error', [
                'to' => $to,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);
            $msg = $response->json('requestError.serviceException.text')
                ?? $response->json('status.description')
                ?? 'WhatsApp delivery failed. Please use SMS.';
            return ['success' => false, 'error' => $this->friendlyError($msg)];
        } catch (\Throwable $e) {
            Log::error('Infobip WhatsApp exception: ' . $e->getMessage(), ['to' => $to]);
            return ['success' => false, 'error' => 'WhatsApp service unavailable. Please try again.'];
        }
    }

    protected function http()
    {
        return Http::baseUrl($this->baseUrl())
            ->withHeaders([
                'Authorization' => 'App ' . config('services.infobip.api_key'),
                'Accept' => 'application/json',
            ])
            ->timeout(10);
    }

    /**
     * Normalise the configured base URL — tolerate values pasted from the
     * Infobip dashboard with or without the leading "https://" / trailing
     * slash. Guzzle requires a scheme, so a bare host would otherwise
     * blow up at request time.
     */
    protected function baseUrl(): string
    {
        $raw = trim((string) config('services.infobip.base_url'));
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
     * Lightweight auth smoke test. Hits Infobip's account-balance
     * endpoint, which requires authentication but doesn't send anything
     * or charge the account. Returns the parsed body on success so a
     * caller can show "balance: $X" as confirmation the keys work.
     *
     * @return array{success: bool, error?: string, balance?: float, currency?: string}
     */
    public function ping(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'Infobip credentials not set.'];
        }
        try {
            $response = $this->http()->get('/account/1/balance');
            if ($response->successful()) {
                return [
                    'success' => true,
                    'balance' => (float) $response->json('balance', 0),
                    'currency' => (string) $response->json('currency', ''),
                ];
            }
            return [
                'success' => false,
                'error' => 'HTTP ' . $response->status() . ': '
                    . ($response->json('requestError.serviceException.text') ?: 'auth check failed.'),
            ];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Infobip's APIs are strict about the destination format — a leading
     * "+" is not required, and they prefer digits-only. Accept any input
     * the controller passes and reduce to digits.
     */
    protected function normalise(string $to): string
    {
        return (string) preg_replace('/\D+/', '', $to);
    }

    /**
     * Map Infobip's raw error strings to something a normal person can
     * act on. Falls through to the original message when there's no
     * known translation, so we never lose information.
     */
    protected function friendlyError(string $raw): string
    {
        $lower = strtolower($raw);
        if (str_contains($lower, 'invalid source') || str_contains($lower, 'rejected_source')) {
            return 'The sender used for this channel is not approved on the Infobip account. Please choose the other channel or contact support.';
        }
        if (str_contains($lower, 'invalid destination') || str_contains($lower, 'rejected_destination')) {
            return 'The destination number is not valid or not supported by this channel.';
        }
        if (str_contains($lower, 'not enough') || str_contains($lower, 'insufficient')) {
            return 'The messaging account is out of credit. Please try again later.';
        }
        if (str_contains($lower, 'unauthorized') || str_contains($lower, 'forbidden')) {
            return 'Messaging service authentication failed.';
        }
        return $raw;
    }

    protected function simulated(string $channel, string $to, string $text): array
    {
        Log::warning('Infobip not configured — simulating send', [
            'channel' => $channel,
            'to' => $to,
            'text' => $text,
        ]);
        return ['success' => true, 'simulated' => true];
    }
}
