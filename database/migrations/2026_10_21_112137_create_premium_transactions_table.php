<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Premium purchase / renewal ledger. One row per successful payment so the
 * Membership Management screen can show full "Payment & Renewal History".
 * Keeping it as a separate table (instead of just the latest-on-user) lets
 * us retain history across plan changes and refunds.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('premium_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('premium_plan_id')->nullable();
            $table->string('plan_name_snapshot');    // snapshot so renaming the plan later doesn't rewrite history
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_method');        // 'wallet' | 'card' | 'free'
            $table->string('status')->default('completed'); // completed | failed | refunded
            $table->string('reference_id')->nullable();     // external gateway ref if any
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('premium_transactions');
    }
};
