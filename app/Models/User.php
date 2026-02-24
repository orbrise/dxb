<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'password',
        'email_verified_at',
        'verification_code',
        'type',
        'agency_type',
        'verified',
        'registration_ip',
        'registration_country',
        'about',
        'avatar',
        'status',
        'phone',
        'country_code',
    ];

    /**p
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'pending',
        'verified' => 0,
    ];

    public function getcity() {
        return $this->hasOne(City::class, 'id', 'city');
    }

    public function getincall() {
        return $this->hasOne(Currency::class, 'id', 'incallcurr');
    }

    public function getoutcall() {
        return $this->hasOne(Currency::class, 'id', 'outcallcurr');
    }

    public function getprofile(){
        return $this->hasMany(UsersProfile::class, 'user_id', 'id');
    }

    public function profiles()
{
    return $this->hasMany(UsersProfile::class);
}

public function wallet()
{
    return $this->hasOne(Wallet::class);
}

public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function favoritedProfiles()
{
    return $this->belongsToMany(UsersProfile::class, 'favorites', 'user_id', 'profile_id');
}

public function newsletterSubscriptions()
{
    return $this->hasMany(NewsletterSubscription::class);
}

public function newsletterGenders()
{
    return $this->hasMany(NewsletterGender::class);
}


}
