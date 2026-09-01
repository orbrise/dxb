<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->string('reference', 100)->nullable()->after('user_id');
            $table->string('error_code', 100)->nullable()->after('reference');
            $table->string('decline_code', 100)->nullable()->after('error_code');
            $table->text('error_message')->nullable()->after('decline_code');

            $table->index('reference');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex(['reference']);
            $table->dropIndex(['status']);
            $table->dropColumn(['reference', 'error_code', 'decline_code', 'error_message']);
        });
    }
};
