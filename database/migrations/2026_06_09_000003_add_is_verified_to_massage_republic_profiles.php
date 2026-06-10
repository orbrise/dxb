<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('massage_republic_profiles', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('rating');
            }
        });
    }

    public function down(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('massage_republic_profiles', 'is_verified')) {
                $table->dropColumn('is_verified');
            }
        });
    }
};
