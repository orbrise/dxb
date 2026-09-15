<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Durable queue of WebRTC signaling messages. Used as a fallback path
     * when Reverb broadcast delivery is flaky — the callee polls this
     * table every ~1.5s and processes any signals directed at them that
     * haven't been marked delivered yet.
     *
     * Rows are short-lived; a scheduled task (or the polling itself) can
     * hard-delete anything older than a few minutes.
     */
    public function up(): void
    {
        Schema::create('call_signals', function (Blueprint $table) {
            $table->id();
            $table->string('call_id', 40);
            $table->unsignedBigInteger('from_user_id');
            $table->unsignedBigInteger('to_user_id');
            $table->string('type', 16);         // offer|answer|ice|hangup|decline|ringing
            $table->string('call_type', 8);     // audio|video
            $table->longText('payload')->nullable(); // JSON — SDP / ICE candidate
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['to_user_id', 'delivered_at', 'created_at']);
            $table->index('call_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_signals');
    }
};
