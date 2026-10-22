<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Track per-user premium subscription state.
 *   premium_plan_id    — nullable FK to premium_plans; which plan they're on
 *   premium_expires_at — nullable timestamp; past = inactive, future = active
 * Both nullable because the vast majority of users won't have a subscription.
 * A simple "is active?" check is premium_expires_at > now().
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('premium_plan_id')->nullable()->after('id');
            $table->timestamp('premium_expires_at')->nullable()->after('premium_plan_id');
            $table->index(['premium_expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['premium_expires_at']);
            $table->dropColumn(['premium_plan_id', 'premium_expires_at']);
        });
    }
};
