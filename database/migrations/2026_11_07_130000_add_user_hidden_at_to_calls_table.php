<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            // "Delete call" in a user's own call history. One column per
            // side so deleting it on one end leaves the other person's
            // history (and the admin's inbox_hidden_at) untouched.
            if (!Schema::hasColumn('calls', 'caller_hidden_at')) {
                $table->timestamp('caller_hidden_at')->nullable();
            }
            if (!Schema::hasColumn('calls', 'callee_hidden_at')) {
                $table->timestamp('callee_hidden_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        foreach (['caller_hidden_at', 'callee_hidden_at'] as $column) {
            if (Schema::hasColumn('calls', $column)) {
                Schema::table('calls', fn (Blueprint $table) => $table->dropColumn($column));
            }
        }
    }
};
