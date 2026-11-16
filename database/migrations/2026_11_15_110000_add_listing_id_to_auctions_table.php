<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Top Spot auctions for a non-escort listing category (Massage,
        // Phone & Cam, …). NULL = an escort auction, keyed by `gender` as before.
        if (!Schema::hasColumn('auctions', 'listing_id')) {
            Schema::table('auctions', function (Blueprint $table) {
                $table->unsignedBigInteger('listing_id')->nullable()->after('gender')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('auctions', 'listing_id')) {
            Schema::table('auctions', function (Blueprint $table) {
                $table->dropIndex(['listing_id']);
                $table->dropColumn('listing_id');
            });
        }
    }
};
