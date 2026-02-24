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
        Schema::create('url_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('custom_url')->unique(); // e.g., 'all_female-escorts-in-dubai'
            $table->string('base_pattern'); // e.g., 'female-escorts-in-dubai'
            $table->enum('redirect_type', ['301', '302', 'canonical'])->default('301');
            $table->text('description')->nullable(); // Admin notes
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['custom_url', 'is_active']);
            $table->index('base_pattern');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_aliases');
    }
};
