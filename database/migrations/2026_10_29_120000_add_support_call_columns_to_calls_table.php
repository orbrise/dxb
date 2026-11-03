<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Support-call extension for the calls table.
 *
 *  - callee_id → nullable: a user-to-support call has no fixed callee at
 *    create time; the first admin to tap Accept claims the row via an
 *    atomic UPDATE in CallController::supportAccept().
 *  - is_support (bool): lets the UI + queries fan-out routing decisions
 *    (broadcast on support-inbox vs single chat.{id}, show "Support call"
 *    vs peer name in history, etc.) without having to join or infer from
 *    callee_id being null (which also happens briefly for abandoned calls).
 */
return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK before changing nullability — SQLite + older MySQL
        // builds reject ALTER COLUMN on a column that participates in an FK.
        Schema::table('calls', function (Blueprint $table) {
            $table->dropForeign(['callee_id']);
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->unsignedBigInteger('callee_id')->nullable()->change();
            $table->boolean('is_support')->default(false)->after('type')->index();
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->foreign('callee_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->dropForeign(['callee_id']);
            $table->dropColumn('is_support');
        });

        // Any null rows must be removed before restoring the NOT NULL
        // constraint, otherwise the ALTER would fail on reversal.
        DB::table('calls')->whereNull('callee_id')->delete();

        Schema::table('calls', function (Blueprint $table) {
            $table->unsignedBigInteger('callee_id')->nullable(false)->change();
            $table->foreign('callee_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }
};
