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
        Schema::table('profile_images', function (Blueprint $table) {
            $table->integer('image_order')->default(0)->after('is_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_images', function (Blueprint $table) {
            $table->dropColumn('image_order');
        });
    }
};
