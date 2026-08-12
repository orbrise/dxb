<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scraper_runs', function (Blueprint $table) {
            $table->id();
            $table->string('source', 40)->index();
            $table->string('city_slug', 80);
            $table->unsignedInteger('requested_count');
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedInteger('progress_current')->default(0);
            $table->unsignedInteger('progress_total')->default(0);
            $table->string('progress_stage', 60)->nullable();
            $table->integer('exit_code')->nullable();
            $table->string('log_path')->nullable();
            $table->text('error_message')->nullable();
            $table->unsignedBigInteger('started_by')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scraper_runs');
    }
};
