<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('massage_republic_profiles', 'is_premium')) {
                $table->boolean('is_premium')->default(false)->after('is_verified');
            }
        });
    }

    public function down(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('massage_republic_profiles', 'is_premium')) {
                $table->dropColumn('is_premium');
            }
        });
    }
};
