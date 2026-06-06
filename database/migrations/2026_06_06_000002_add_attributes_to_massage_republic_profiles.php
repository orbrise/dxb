<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            $table->json('attributes')->nullable()->after('image_urls');
            $table->decimal('incall_price', 10, 2)->nullable()->after('attributes');
            $table->decimal('outcall_price', 10, 2)->nullable()->after('incall_price');
            $table->string('incall_currency', 8)->nullable()->after('outcall_price');
            $table->string('outcall_currency', 8)->nullable()->after('incall_currency');
        });
    }

    public function down(): void
    {
        Schema::table('massage_republic_profiles', function (Blueprint $table) {
            $table->dropColumn(['attributes', 'incall_price', 'outcall_price', 'incall_currency', 'outcall_currency']);
        });
    }
};
