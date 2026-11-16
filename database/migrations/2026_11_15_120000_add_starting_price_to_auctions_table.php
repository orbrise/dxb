<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('auctions', 'starting_price')) return;

        // The price every round of a spot opens at. current_price climbs with
        // the bids, so a new round used to start at the last winning bid.
        Schema::table('auctions', function (Blueprint $table) {
            $table->decimal('starting_price', 10, 2)->nullable()->after('spot_number');
        });

        // Existing spots: never bid on → the current price is still the
        // opening price; otherwise the first bid had to beat it by $10.
        DB::statement("
            UPDATE auctions a
            LEFT JOIN (
                SELECT auction_id, MIN(amount) AS lowest
                FROM auction_bids GROUP BY auction_id
            ) b ON b.auction_id = a.id
            SET a.starting_price = IF(b.lowest IS NULL, a.current_price, GREATEST(b.lowest - 10, 0))
        ");

        // Open rounds with no bids yet that kept the last round's winning
        // price (admin Reset didn't lower it) open at the starting price.
        DB::statement("
            UPDATE auctions a
            SET a.current_price = a.starting_price
            WHERE a.status = 'active'
              AND a.current_price > a.starting_price
              AND NOT EXISTS (SELECT 1 FROM auction_bids b WHERE b.auction_id = a.id AND b.status = 'active')
        ");
    }

    public function down(): void
    {
        if (Schema::hasColumn('auctions', 'starting_price')) {
            Schema::table('auctions', fn (Blueprint $table) => $table->dropColumn('starting_price'));
        }
    }
};
