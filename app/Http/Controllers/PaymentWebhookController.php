<?php

namespace App\Http\Controllers;

use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Receives failure notifications from the myads Stripe webhook so that we
     * still record a `wallet_transactions` row when the customer's browser
     * never posted the failure back to us (closed popup, lost tab, etc.).
     * The frontend postMessage flow remains the fast path; this is the
     * belt-and-suspenders that guarantees we eventually see the failure.
     */
    public function primary(Request $request)
    {
        $secret = env('PRIMARY_GATEWAY_WEBHOOK_SECRET');
        if (empty($secret)) {
            Log::warning('Primary gateway webhook rejected: PRIMARY_GATEWAY_WEBHOOK_SECRET not configured');
            return response()->json(['error' => 'Webhook not configured'], 500);
        }

        $body = $request->getContent();
        $providedSignature = (string) $request->header('X-Payment-Signature', '');
        $expectedSignature = hash_hmac('sha256', $body, $secret);

        if (!hash_equals($expectedSignature, $providedSignature)) {
            Log::warning('Primary gateway webhook rejected: bad signature', [
                'ip' => $request->ip(),
                'has_header' => $providedSignature !== '',
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);
        if (!is_array($data) || empty($data['reference_id'])) {
            return response()->json(['error' => 'Missing reference_id'], 422);
        }

        $referenceId = $data['reference_id'];
        $eventType = $data['event_type'] ?? '';

        // `payment_intent.canceled` fires when Stripe auto-cancels a stale
        // intent (~24h of inactivity) or when it is cancelled explicitly. Treat
        // these as `abandoned` — the customer never completed the payment but
        // it wasn't a hard failure either.
        $newStatus = $eventType === 'payment_intent.canceled' ? 'abandoned' : 'failed';

        // If we already have a row for this reference (frontend beat us to it,
        // or a duplicate webhook delivery), don't insert again. If the existing
        // row is a `completed` one, that means the payment ultimately succeeded
        // and this stale-failure delivery should be ignored.
        $existing = WalletTransaction::where('reference', $referenceId)->first();
        if ($existing) {
            if ($existing->status === 'completed') {
                return response()->json(['status' => 'ignored', 'reason' => 'already completed'], 200);
            }
            // A prior `failed` row is stronger evidence than a later `abandoned`
            // (Stripe often fires payment_failed then later canceled for the same
            // intent). Don't downgrade failed -> abandoned.
            if ($existing->status === 'failed' && $newStatus === 'abandoned') {
                return response()->json(['status' => 'ignored', 'reason' => 'already failed'], 200);
            }
            $defaultMessage = $newStatus === 'abandoned' ? 'Payment was abandoned or auto-cancelled.' : null;
            $existing->fill(array_filter([
                'status' => $newStatus,
                'error_code' => $existing->error_code ?: ($data['error_code'] ?? null),
                'decline_code' => $existing->decline_code ?: ($data['decline_code'] ?? null),
                'error_message' => $existing->error_message ?: ($data['error_message'] ?? $defaultMessage),
            ]))->save();
            return response()->json(['status' => 'updated', 'row_status' => $newStatus], 200);
        }

        $userId = null;
        if (!empty($data['customer_email'])) {
            $user = \App\Models\User::where('email', $data['customer_email'])->first();
            if ($user) {
                $userId = $user->id;
            }
        }

        $description = $newStatus === 'abandoned'
            ? sprintf('Payment abandoned on primary gateway (Stripe webhook) - Ref: %s', $referenceId)
            : sprintf('Payment failed on primary gateway (Stripe webhook) - Ref: %s', $referenceId);

        WalletTransaction::create([
            'user_id' => $userId,
            'wallet_id' => null,
            'amount' => $data['amount'] ?? 0,
            'type' => strpos((string) $referenceId, 'CREDITS_') === 0 ? 'credit_purchase' : 'primary_gateway_payment',
            'status' => $newStatus,
            'payment_method' => 'primary_gateway',
            'description' => $description,
            'package_id' => $data['package_id'] ?? null,
            'reference' => $referenceId,
            'error_code' => $data['error_code'] ?? null,
            'decline_code' => $data['decline_code'] ?? null,
            'error_message' => $data['error_message'] ?? ($newStatus === 'abandoned' ? 'Payment was abandoned or auto-cancelled.' : null),
        ]);

        return response()->json(['status' => 'created', 'row_status' => $newStatus], 200);
    }
}
