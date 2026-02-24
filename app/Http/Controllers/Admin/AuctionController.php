<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\City;
use Carbon\Carbon;

class AuctionController extends Controller
{
    public function index(Request $request)
{
    $query = Auction::with(['city', 'winnerProfile', 'bids'])
        ->withCount('bids');

    // Update this line to use city_id instead of city
    if ($request->filled('city_id')) {
        $query->where('city_id', $request->city_id);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }

    if ($request->filled('spot')) {
        $query->where('spot_number', $request->spot);
    }

    // Apply sorting
    switch ($request->get('sort', 'end_date')) {
        case 'current_price':
            $query->orderBy('current_price', 'desc');
            break;
        case 'bid_count':
            $query->orderBy('bid_count', 'desc');
            break;
        default:
            $query->orderBy('end_date', 'asc');
    }

    $auctions = $query->paginate(15)->withQueryString();
    $cities = City::orderBy('name')->get();

    return view('admin.auctions.index', compact('auctions', 'cities'));
}
    
    public function create()
    {
        $cities = City::all();
        return view('admin.auctions.create', compact('cities'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'spot_number' => 'required|integer|min:1|max:6',
            'city_id' => 'required|exists:cities,id',
            'gender' => 'required|in:female,male,shemale',
            'starting_price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1|max:30',
        ]);
        
        // Check if an auction already exists for this spot in this city
        $existingAuction = Auction::where('spot_number', $request->spot_number)
            ->where('city_id', $request->city_id)
            ->where('gender', $request->gender)
            ->where('status', 'active')
            ->first();
            
        if ($existingAuction) {
            return redirect()->back()->with('error', 'An active auction already exists for this spot in this city.');
        }
        
        // Create new auction
        Auction::create([
            'spot_number' => $request->spot_number,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'current_price' => $request->starting_price,
            'end_date' => Carbon::now()->addDays($request->duration_days),
            'status' => 'active',
        ]);
        
        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction spot created successfully.');
    }
    
    public function edit(Auction $auction)
    {
        $cities = City::all();
        return view('admin.auctions.edit', compact('auction', 'cities'));
    }
    
    public function update(Request $request, Auction $auction)
    {
        $request->validate([
            'spot_number' => 'required|integer|min:1|max:6',
            'city_id' => 'required|exists:cities,id',
            'gender' => 'required|in:female,male,shemale',
            'current_price' => 'required|numeric|min:0',
            'end_date' => 'required|date|after:now',
            'status' => 'required|in:active,ended',
        ]);
        
        // Check if we're changing spot/city/gender and if a conflict exists
        if ($auction->spot_number != $request->spot_number || 
            $auction->city_id != $request->city_id || 
            $auction->gender != $request->gender) {
            
            $existingAuction = Auction::where('spot_number', $request->spot_number)
                ->where('city_id', $request->city_id)
                ->where('gender', $request->gender)
                ->where('status', 'active')
                ->where('id', '!=', $auction->id)
                ->first();
                
            if ($existingAuction) {
                return redirect()->back()->with('error', 'An active auction already exists for this spot in this city.');
            }
        }
        
        $auction->update([
            'spot_number' => $request->spot_number,
            'city_id' => $request->city_id,
            'gender' => $request->gender,
            'current_price' => $request->current_price,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);
        
        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction updated successfully.');
    }
    
    public function destroy(Auction $auction)
    {
        // Check if auction has bids
        if ($auction->bids()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete an auction with bids.');
        }
        
        $auction->delete();
        
        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction deleted successfully.');
    }
    
    public function endAuction(Auction $auction)
    {
        // Find the highest bid
        $highestBid = $auction->bids()
            ->orderBy('amount', 'desc')
            ->first();
            
        if ($highestBid) {
            // Update auction with winner
            $auction->update([
                'status' => 'ended',
                'winner_id' => $highestBid->user_id,
                'winner_profile_id' => $highestBid->profile_id,
            ]);
            
            // Update bid status
            $highestBid->update(['status' => 'won']);
            
            // Update other bids as lost
            $auction->bids()
                ->where('id', '!=', $highestBid->id)
                ->update(['status' => 'lost']);
                
            return redirect()->route('admin.auctions.index')
                ->with('success', 'Auction ended successfully with a winner.');
        } else {
            // No bids, just end the auction
            $auction->update(['status' => 'ended']);
            
            return redirect()->route('admin.auctions.index')
                ->with('success', 'Auction ended without any bids.');
        }
    } 
    
    public function resetAuction(Auction $auction)
    {
        // Reset the existing auction to active status
        $auction->update([
            'status' => 'active',
            'end_date' => Carbon::now()->addDays(7), // Reset end date to 7 days from now
            'winner_id' => null,
            'winner_profile_id' => null,
        ]);
        
        return redirect()->route('admin.auctions.index')
            ->with('success', 'Auction has been reset and is now active again.');
    }
    
    public function bids(Auction $auction)
    {
        $bids = $auction->bids()
            ->with(['user', 'profile'])
            ->orderBy('amount', 'desc')
            ->paginate(20);
            
        return view('admin.auctions.bids', compact('auction', 'bids'));
    }

    public function awardSpot(Auction $auction, AuctionBid $bid)
    {
        // Check if the bid belongs to this auction
        if ($bid->auction_id !== $auction->id) {
            return redirect()->back()
                ->with('error', 'Invalid bid for this auction.');
        }

        // Update auction with winner details
        $auction->update([
            'status' => 'ended',
            'winner_id' => $bid->user_id,
            'winner_profile_id' => $bid->profile_id,
            'current_price' => $bid->amount,
        ]);

        // Update all bids status
        $auction->bids()->where('id', $bid->id)->update(['status' => 'won']);
        $auction->bids()->where('id', '!=', $bid->id)->update(['status' => 'lost']);

        return redirect()->back()
            ->with('success', 'Auction spot awarded successfully to ' . ($bid->profile->name ?? 'the bidder') . '!');
    }

    public function updateCity(Request $request)
{
    $auction = Auction::findOrFail($request->auction_id);
    $auction->update(['city_id' => $request->city_id]);
    return response()->json(['success' => true]);
}
}
