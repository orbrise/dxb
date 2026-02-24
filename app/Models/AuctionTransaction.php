<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuctionTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'auction_id',
        'bid_id',
        'amount',
        'status', // 'pending', 'completed', 'refunded'
        'payment_method',
        'transaction_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function auction()
    {
        return $this->belongsTo(Auction::class);
    }

    public function bid()
    {
        return $this->belongsTo(AuctionBid::class, 'bid_id');
    }
}
