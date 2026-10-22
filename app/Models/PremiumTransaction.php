<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PremiumTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'premium_plan_id',
        'plan_name_snapshot',
        'amount',
        'payment_method',
        'status',
        'reference_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(PremiumPlan::class, 'premium_plan_id');
    }
}
