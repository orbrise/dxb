<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The claim flow uses Infobip raw SMS / WhatsApp — we generate, expire,
 * and check the OTP code ourselves rather than delegating to a hosted
 * verification product. Add the two columns that lifecycle needs:
 *   - otp_hash:   bcrypt of the 6-digit code (never store plaintext).
 *   - expires_at: cutoff after which verification returns "expired".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_claim_attempts', function (Blueprint $table) {
            $table->string('otp_hash', 191)->nullable()->after('email');
            $table->timestamp('expires_at')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('profile_claim_attempts', function (Blueprint $table) {
            $table->dropColumn(['otp_hash', 'expires_at']);
        });
    }
};
