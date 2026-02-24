<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->boolean('send_on_account_create')->default(true)->after('mail_from_name');
            $table->boolean('send_on_forget_password')->default(true)->after('send_on_account_create');
            $table->boolean('send_on_profile_upgrade')->default(true)->after('send_on_forget_password');
            $table->boolean('send_on_account_upgrade')->default(true)->after('send_on_profile_upgrade');
            $table->boolean('send_on_profile_archived')->default(true)->after('send_on_account_upgrade');
            $table->boolean('send_on_verification')->default(true)->after('send_on_profile_archived');
            $table->boolean('send_on_package_purchase')->default(true)->after('send_on_verification');
            $table->boolean('send_on_wallet_transaction')->default(false)->after('send_on_package_purchase');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->dropColumn([
                'send_on_account_create',
                'send_on_forget_password',
                'send_on_profile_upgrade',
                'send_on_account_upgrade',
                'send_on_profile_archived',
                'send_on_verification',
                'send_on_package_purchase',
                'send_on_wallet_transaction'
            ]);
        });
    }
};
