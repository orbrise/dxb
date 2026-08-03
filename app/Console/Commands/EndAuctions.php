<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\AuctionTransaction;
use App\Mail\AuctionSpotLost;
use App\Mail\AuctionSpotWon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                    'winner_id' => $highestBid->user_id,
                    'winner_profile_id' => $highestBid->profile_id,
                    'status' => 'completed'
                ]);

                // Mark the winning bid as won and every other bid on this
                // auction as lost. Previously the cron path skipped this
                // and only the AuctionTransaction row was updated, so bid
                // rows kept `status=active` forever and losing bidders
                // never received a notification.
                $highestBid->update(['status' => 'won']);
                AuctionBid::where('auction_id', $auction->id)
                    ->where('id', '!=', $highestBid->id)
                    ->update(['status' => 'lost']);

                // Process the winning transaction
                AuctionTransaction::where('bid_id', $highestBid->id)
                    ->update(['status' => 'completed']);

                // Congratulate the winner + email losing bidders about
                // the result and wallet refund.
                $this->notifyWinner($auction, $highestBid);
                $this->notifyLosingBidders($auction, $highestBid);

                // Create a new auction for the next period
                $newAuction = $auction->replicate();
                $newAuction->status = 'active';
                $newAuction->winner_profile_id = null;
                $newAuction->current_price = $auction->starting_bid;
                $newAuction->end_date = Carbon::now()->addDays($auction->duration_days ?? 7);
                $newAuction->save();

                $this->info("Auction #{$auction->id} ended. Winner: Profile #{$highestBid->profile_id} with bid of {$highestBid->amount}");
            } else {
                // No bids — keep the auction alive instead of ending it.
                // Extend end_date by 7 days so the spot remains biddable
                // in the next cycle. Spot #, city, gender, price, and
                // background image are preserved on the same row (no
                // replicate). Status stays 'active' so the frontend keeps
                // showing the "Make Offer" card.
                $newEnd = Carbon::now()->addDays(7);
                $auction->update(['end_date' => $newEnd]);
                $this->info("Auction #{$auction->id} had no bids — extended by 7 days to {$newEnd->toDateTimeString()}");
            }
        }
        
        return Command::SUCCESS;
    }

    private function notifyWinner(Auction $auction, AuctionBid $winningBid): void
    {
        $auction->loadMissing('city');
        $winningBid->loadMissing(['user:id,name,email', 'profile:id,name,slug']);

        $user = $winningBid->user;
        if (! $user || ! $user->email) {
            return;
        }

        $cityName = $auction->city->name ?? 'your city';
        $citySlug = strtolower($cityName);
        $durationDays = $auction->duration_days ?? 7;
        $spotExpiryDate = $auction->end_date
            ? Carbon::parse($auction->end_date)->format('M j, Y')
            : null;

        $profileName = $winningBid->profile->name ?? 'your profile';
        $profileUrl = $winningBid->profile
            ? url('/' . $auction->gender . '-escorts-in-' . $citySlug . '/' . $winningBid->profile->id . '/' . ($winningBid->profile->slug ?? ''))
            : url('/');

        try {
            Mail::to($user->email)->send(new AuctionSpotWon([
                'userName'       => $user->name ?: 'there',
                'profileName'    => $profileName,
                'spotNumber'     => $auction->spot_number,
                'cityName'       => $cityName,
                'gender'         => $auction->gender,
                'winningBid'     => $winningBid->amount,
                'durationDays'   => $durationDays,
                'spotExpiryDate' => $spotExpiryDate,
                'profileUrl'     => $profileUrl,
            ]));
        } catch (\Throwable $e) {
            Log::warning('AuctionSpotWon mail failed for bid #' . $winningBid->id . ': ' . $e->getMessage());
        }
    }

    private function notifyLosingBidders(Auction $auction, AuctionBid $winningBid): void
    {
        $auction->loadMissing('city');
        $cityName = $auction->city->name ?? 'your city';
        $auctionsUrl = url('/auctions/' . $auction->gender . '-escorts-in-' . strtolower($cityName));

        $losingBids = AuctionBid::with('user:id,name,email')
            ->where('auction_id', $auction->id)
            ->where('id', '!=', $winningBid->id)
            ->get();

        foreach ($losingBids as $bid) {
            if (! $bid->user || ! $bid->user->email) {
                continue;
            }
            try {
                Mail::to($bid->user->email)->send(new AuctionSpotLost([
                    'userName'     => $bid->user->name ?: 'there',
                    'spotNumber'   => $auction->spot_number,
                    'cityName'     => $cityName,
                    'gender'       => $auction->gender,
                    'yourBid'      => $bid->amount,
                    'winningBid'   => $winningBid->amount,
                    'refundAmount' => $bid->amount,
                    'auctionsUrl'  => $auctionsUrl,
                ]));
            } catch (\Throwable $e) {
                Log::warning('AuctionSpotLost mail failed for bid #' . $bid->id . ': ' . $e->getMessage());
            }
        }
    }
}
