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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('auto_archive_enabled')->default(false);
            $table->integer('auto_archive_days')->default(30);
            $table->boolean('send_archive_warning')->default(true);
            $table->integer('archive_warning_days')->default(3);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'auto_archive_enabled',
                'auto_archive_days', 
                'send_archive_warning',
                'archive_warning_days'
            ]);
        });
    }
};
