<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->integer('spot_number');
            $table->decimal('current_price', 10, 2);
            $table->datetime('end_date');
            $table->string('status')->default('active');
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('winner_profile_id')->nullable()->constrained('users_profiles')->nullOnDelete();
            $table->foreignId('city_id')->constrained('cities');
            $table->string('gender')->default('female');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auctions');
    }
};
