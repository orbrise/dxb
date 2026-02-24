<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id', 'amount', 'type', 'description', 'status', 'package_id', 'user_id'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

}
