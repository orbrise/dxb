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
        Schema::create('url_redirects', function (Blueprint $table) {
            $table->id();
            $table->string('link_from');
            $table->string('link_to');
            $table->boolean('is_direct_link')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('link_from');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('url_redirects');
    }
};
