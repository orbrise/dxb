<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\AuctionTransaction;
use Carbon\Carbon;

class EndAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End expired auctions and assign winners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        
        // Find all active auctions that have ended
        $endedAuctions = Auction::where('status', 'active')
            ->where('end_date', '<', $now)
            ->get();
            
        $this->info("Found {$endedAuctions->count()} auctions to end");
        
        foreach ($endedAuctions as $auction) {
            // Find the highest bid
            $highestBid = AuctionBid::where('auction_id', $auction->id)
                ->where('status', 'active')
                ->orderBy('amount', 'desc')
                ->first();
                
            if ($highestBid) {
                // Set the winner
                $auction->update([
                    'winner_profile_id' => $highestBid->profile_id,
                    'status' => 'completed'
                ]);
                
                // Process the winning transaction
                AuctionTransaction::where('bid_id', $highestBid->id)
                    ->update(['status' => 'completed']);
                    
                // Create a new auction for the next period
                $newAuction = $auction->replicate();
                $newAuction->status = 'active';
                $newAuction->winner_profile_id = null;
                $newAuction->current_price = $auction->starting_bid;
                $newAuction->end_date = Carbon::now()->addDays($auction->duration_days ?? 7);
                $newAuction->save();
                
                $this->info("Auction #{$auction->id} ended. Winner: Profile #{$highestBid->profile_id} with bid of {$highestBid->amount}");
            } else {
                // No bids, just mark as completed
                $auction->update(['status' => 'completed']);
                
                // Create a new auction for the next period
                $newAuction = $auction->replicate();
                $newAuction->status = 'active';
                $newAuction->winner_profile_id = null;
                $newAuction->current_price = $auction->starting_bid;
                $newAuction->end_date = Carbon::now()->addDays($auction->duration_days ?? 7);
                $newAuction->save();
                
                $this->info("Auction #{$auction->id} ended with no bids");
            }
        }
        
        return Command::SUCCESS;
    }
}
