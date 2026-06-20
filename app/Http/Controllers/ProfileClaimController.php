<?php

namespace App\Http\Controllers;

use App\Mail\ProfileClaimCredentials;
use App\Models\User;
use App\Models\UsersProfile;
use App\Services\InfobipMessagingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * "Claim Your Profile" flow.
 *
 * Background: profiles are imported from upstream directories. Each
 * imported profile already has a placeholder user (synthetic email,
 * random password) so the relational graph holds together. When the
 * real owner finds their listing on evoory, they prove ownership by
 * receiving an OTP on the phone number the upstream import recorded —
 * and at the same time provide an email so we can transfer the
 * placeholder user record into their hands.
 *
 * Flow:
 *   POST  /profile/{id}/claim/send-otp     ← validates phone match + email
 *                                             availability, generates a
 *                                             6-digit OTP, hashes it into
 *                                             profile_claim_attempts, and
 *                                             ships the plaintext to the
 *                                             user via Infobip (SMS or
 *                                             WhatsApp).
 *   POST  /profile/{id}/claim/verify-otp   ← compares the entered code
 *                                             against the stored hash on
 *                                             the latest attempt; on match
 *                                             updates the placeholder user
 *                                             with the provided email + a
 *                                             new generated password,
 *                                             claims every other profile
 *                                             that shares the same phone
 *                                             (per explicit user
 *                                             instruction), emails the
 *                                             credentials, and logs the
 *                                             user in.
 *
 * Phone matching is done after normalising to digits only, so "+971 552
 * 092466" and "00971552092466" both match the stored "971552092466".
 */
class ProfileClaimController extends Controller
{
    // Local-OTP lifecycle constants. Infobip just delivers the text; we
    // own generation, expiry, and verification.
    private const OTP_LENGTH = 6;
    // Tracks the WhatsApp `authentication` template's hard-coded footer
    // ("Expires in 5 minutes.") so the code we accept locally matches what
    // the user reads on the device. Adjust both in lockstep if you swap
    // templates.
    private const OTP_TTL_MINUTES = 5;

    public function __construct(private InfobipMessagingService $messenger)
    {
    }

    public function sendOtp(Request $request, int $profileId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone'   => ['required', 'string', 'min:6', 'max:32'],
            'email'   => ['required', 'email', 'max:191'],
            'channel' => ['required', 'in:whatsapp,sms'],
        ]);
        if ($validator->fails()) {
            return response()->json(['ok' => false, 'message' => $validator->errors()->first()], 422);
        }

        $profile = UsersProfile::find($profileId);
        if (!$profile) {
            return response()->json(['ok' => false, 'message' => 'Profile not found.'], 404);
        }

        $email = strtolower(trim($request->input('email')));
        $phoneRaw = $request->input('phone');
        $phoneDigits = $this->normalisePhone($phoneRaw);
        if ($phoneDigits === '') {
            return response()->json(['ok' => false, 'message' => 'Please enter a valid phone number.'], 422);
        }

        // Reject early when the entered email already belongs to a real
        // (not-placeholder-for-this-claim) user. We can't merge accounts
        // here without risking cross-account leakage.
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'That email is already registered. Please sign in instead, or use a different email.',
            ], 422);
        }

        // Match the entered phone against the profile's recorded numbers.
        // We compare on the digit-only suffix so countries/formatting drift
        // doesn't cause false negatives.
        if (!$this->profilePhoneMatches($profile, $phoneDigits)) {
            return response()->json([
                'ok' => false,
                'message' => 'Phone number is not matched with the profile on file.',
            ], 422);
        }

        // Cooldown: 60s between sends for the same (profile, phone) pair,
        // up to 6 sends per phone per hour. Infobip will also throttle
        // upstream, but we don't want to burn quota on retry floods.
        $cooldownHit = \DB::table('profile_claim_attempts')
            ->where('profile_id', $profileId)
            ->where('phone', $phoneDigits)
            ->where('created_at', '>=', Carbon::now()->subSeconds(60))
            ->exists();
        if ($cooldownHit) {
            return response()->json([
                'ok' => false,
                'message' => 'Please wait a minute before requesting another code.',
            ], 429);
        }
        $recentSends = \DB::table('profile_claim_attempts')
            ->where('phone', $phoneDigits)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();
        if ($recentSends >= 6) {
            return response()->json([
                'ok' => false,
                'message' => 'Too many attempts. Please try again in an hour.',
            ], 429);
        }

        // Generate a fresh OTP locally. We store only the hash; the
        // plaintext is only ever seen by the messaging service and the
        // user's handset. Re-roll on every send so resends don't accept
        // an older code.
        $code = $this->generateCode();
        $codeHash = Hash::make($code);
        $expiresAt = Carbon::now()->addMinutes(self::OTP_TTL_MINUTES);

        $body = "Your evoory profile claim code is: {$code}. It expires in "
            . self::OTP_TTL_MINUTES . ' minutes. Do not share this code.';

        $e164 = '+' . $phoneDigits;
        $channel = $request->input('channel');
        // WhatsApp uses the authentication template path (sendWhatsAppOtp),
        // which delivers to any recipient without requiring an inbound
        // message in the previous 24 hours. SMS stays free-form.
        $result = $channel === 'whatsapp'
            ? $this->messenger->sendWhatsAppOtp($e164, $code, $body)
            : $this->messenger->sendSms($e164, $body);
        if (!$result['success']) {
            return response()->json(['ok' => false, 'message' => $result['error'] ?? 'Could not send the code.'], 502);
        }

        \DB::table('profile_claim_attempts')->insert([
            'profile_id' => $profileId,
            'phone'      => $phoneDigits,
            'email'      => $email,
            'channel'    => $channel,
            'otp_hash'   => $codeHash,
            'expires_at' => $expiresAt,
            'sent_at'    => Carbon::now(),
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        return response()->json(['ok' => true, 'message' => 'Code sent.']);
    }

    public function verifyOtp(Request $request, int $profileId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'string', 'min:6', 'max:32'],
            'email' => ['required', 'email', 'max:191'],
            'code'  => ['required', 'string', 'min:4', 'max:8'],
        ]);
        if ($validator->fails()) {
            return response()->json(['ok' => false, 'message' => $validator->errors()->first()], 422);
        }

        $profile = UsersProfile::find($profileId);
        if (!$profile) {
            return response()->json(['ok' => false, 'message' => 'Profile not found.'], 404);
        }

        $email = strtolower(trim($request->input('email')));
        $phoneDigits = $this->normalisePhone($request->input('phone'));
        $code = trim($request->input('code'));

        // Re-check email availability — it could have been registered
        // between sendOtp and verifyOtp.
        if (User::where('email', $email)->exists()) {
            return response()->json([
                'ok' => false,
                'message' => 'That email is already registered. Please sign in instead.',
            ], 422);
        }

        $latest = \DB::table('profile_claim_attempts')
            ->where('profile_id', $profileId)
            ->where('phone', $phoneDigits)
            ->orderByDesc('id')
            ->first();
        if (!$latest) {
            return response()->json([
                'ok' => false,
                'message' => 'No verification in progress. Please request a new code.',
            ], 422);
        }
        if ($latest->verify_attempts >= 5) {
            return response()->json([
                'ok' => false,
                'message' => 'Too many failed attempts. Please request a new code.',
            ], 429);
        }

        // Local verification. We compare against the hash on the most
        // recent attempt row, after first checking it isn't expired.
        // In dev mode (Infobip not configured) the simulated-send path
        // still recorded a real hash, so the same check applies — except
        // we also accept the literal "000000" as a tester escape hatch
        // so QA can drive the flow without a working SMS account.
        $hashMatches = $latest->otp_hash && Hash::check($code, $latest->otp_hash);
        $devEscape = app()->environment('local', 'testing')
            && !$this->messenger->isConfigured()
            && $code === '000000';
        $expired = $latest->expires_at && Carbon::parse($latest->expires_at)->isPast();

        if (!$hashMatches && !$devEscape) {
            \DB::table('profile_claim_attempts')
                ->where('id', $latest->id)
                ->update([
                    'verify_attempts' => $latest->verify_attempts + 1,
                    'updated_at' => Carbon::now(),
                ]);
            return response()->json([
                'ok' => false,
                'message' => 'The code is incorrect. Please try again.',
            ], 422);
        }
        if ($expired && !$devEscape) {
            return response()->json([
                'ok' => false,
                'message' => 'The code has expired. Please request a new one.',
            ], 422);
        }

        // Code is approved. Atomically:
        //   1. update the placeholder user (the one currently owning the
        //      claimed profile) with the supplied email + a fresh password
        //      so future logins use real credentials;
        //   2. mark the user verified;
        //   3. ensure every other profile that shares the same phone is
        //      pointed at the same user — per the explicit instruction,
        //      one phone = one owner = all matching profiles in one
        //      dashboard.
        $generatedPassword = Str::random(8) . rand(10, 99);

        $userId = DB::transaction(function () use ($profile, $phoneDigits, $email, $generatedPassword) {
            $user = User::find($profile->user_id);
            if (!$user) {
                // Defensive: imported profile lost its placeholder user
                // somehow. Recreate one so the claim can still complete.
                $user = User::create([
                    'name' => $profile->name ?: 'evoory user',
                    'email' => $email,
                    'password' => bcrypt($generatedPassword),
                    'verified' => 1,
                    'email_verified_at' => Carbon::now(),
                    'type' => 2,
                    'status' => 'active',
                ]);
                $profile->user_id = $user->id;
                $profile->save();
            } else {
                $user->email = $email;
                $user->password = bcrypt($generatedPassword);
                $user->verified = 1;
                $user->email_verified_at = Carbon::now();
                if (empty($user->name)) {
                    $user->name = $profile->name ?: 'evoory user';
                }
                $user->save();
            }

            // Sweep up sibling profiles that share the same phone digits.
            // Match against the digit-suffix of phone/phone2 so reformatted
            // numbers still get picked up. We re-point them at the same
            // owning user record.
            $relatedIds = UsersProfile::query()
                ->where('id', '!=', $profile->id)
                ->where(function ($q) use ($phoneDigits) {
                    $q->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(phone, ''), '+',''), ' ',''), '-',''), '(','') LIKE ?", ['%' . $phoneDigits])
                      ->orWhereRaw("REPLACE(REPLACE(REPLACE(REPLACE(COALESCE(phone2, ''), '+',''), ' ',''), '-',''), '(','') LIKE ?", ['%' . $phoneDigits]);
                })
                ->pluck('id', 'user_id');

            if ($relatedIds->isNotEmpty()) {
                UsersProfile::whereIn('id', $relatedIds->values()->all())
                    ->update(['user_id' => $user->id]);
            }

            return $user->id;
        });

        \DB::table('profile_claim_attempts')
            ->where('id', $latest->id)
            ->update([
                'user_id'     => $userId,
                'verified_at' => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ]);

        // Send credentials email. Failures are non-fatal — the claim has
        // already succeeded; we just log so support can resend manually.
        try {
            $profileCount = UsersProfile::where('user_id', $userId)->count();
            Mail::to($email)->send(new ProfileClaimCredentials([
                'name'          => $profile->name,
                'email'         => $email,
                'password'      => $generatedPassword,
                'profile_count' => $profileCount,
            ]));
        } catch (\Throwable $e) {
            Log::error('Profile claim credentials email failed: ' . $e->getMessage(), [
                'user_id' => $userId,
                'email'   => $email,
            ]);
        }

        Auth::loginUsingId($userId);

        return response()->json([
            'ok' => true,
            'message' => 'Verification successful.',
            'redirect' => url('my-account'),
        ]);
    }

    /**
     * Generate a fresh OTP using crypto-strong randomness. Six digits,
     * zero-padded, so the leading zeros land on the user's screen too.
     */
    protected function generateCode(): string
    {
        $max = (int) str_repeat('9', self::OTP_LENGTH);
        return str_pad((string) random_int(0, $max), self::OTP_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Strip everything but digits. Used as the canonical phone form so
     * "+971 55 209 2466" matches the stored "971552092466" regardless of
     * leading-zero or formatting drift.
     */
    protected function normalisePhone(?string $phone): string
    {
        return (string) preg_replace('/\D+/', '', (string) $phone);
    }

    /**
     * True when the digit-suffix of $profile->phone or $profile->phone2
     * matches the supplied (already-normalised) digits. We compare by
     * suffix so a stored "971552092466" still matches "00971552092466"
     * (international dialling prefix) without bespoke country logic.
     */
    protected function profilePhoneMatches(UsersProfile $profile, string $phoneDigits): bool
    {
        if ($phoneDigits === '') {
            return false;
        }
        foreach ([$profile->phone, $profile->phone2] as $candidate) {
            $candidate = $this->normalisePhone($candidate);
            if ($candidate === '') {
                continue;
            }
            // Match if either string is a suffix of the other — covers
            // both "stored is longer" and "entered is longer" cases.
            if (str_ends_with($candidate, $phoneDigits) || str_ends_with($phoneDigits, $candidate)) {
                return true;
            }
        }
        return false;
    }
}
