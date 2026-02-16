<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use App\Models\Address;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
    ];
    public function items(){
        return $this->hasMany(Item::class);
    }
    public function purchases(){
        return $this->hasMany(Purchase::class);
    }
    public function address(){
        return $this->hasOne(Address::class);
    }
    public function comments(){
        return $this->hasMany(Comments::class);
    }
    public function favorites(){
        return $this->belongsToMany(Item::class, 'favorites')->withTimestamps();
    }
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
