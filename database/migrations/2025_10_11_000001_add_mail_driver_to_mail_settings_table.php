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
            $table->enum('mail_driver', ['smtp', 'sendmail'])->default('smtp')->after('id');
            $table->string('sendmail_path')->nullable()->after('mail_driver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_settings', function (Blueprint $table) {
            $table->dropColumn(['mail_driver', 'sendmail_path']);
        });
    }
};
