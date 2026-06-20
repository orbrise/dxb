<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit + rate-limit table for the "Claim Your Profile" flow.
 *
 * Each row is one OTP send + verification attempt. The OTP itself is
 * stored as a bcrypt hash (see the follow-up migration that adds the
 * otp_hash + expires_at columns); we keep enough metadata here to
 * throttle repeat sends, detect abuse, and reconstruct what happened
 * if a claim turns out to be fraudulent.
 *
 * `verified_at` flips when the user's code matches the stored hash
 * before expiry; until then any send/verify cycle for the same
 * phone+profile within the cooldown window is rejected at the
 * controller level.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_claim_attempts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('profile_id');
            $table->unsignedBigInteger('user_id')->nullable()
                ->comment('The user the profile was transferred to on success.');
            $table->string('phone', 32);
            $table->string('email', 191)->nullable();
            $table->enum('channel', ['whatsapp', 'sms'])->default('whatsapp');
            $table->unsignedTinyInteger('verify_attempts')->default(0)
                ->comment('Count of failed code-check attempts in this session.');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['profile_id', 'phone']);
            $table->index(['phone', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_claim_attempts');
    }
};
