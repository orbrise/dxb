<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'wallet_id', 'amount', 'type', 'description', 'status',
        'payment_method', 'package_id', 'user_id',
        'reference', 'error_code', 'decline_code', 'error_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
