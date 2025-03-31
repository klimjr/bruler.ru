<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted()
    {
        static::creating(function ($user) {
            $user->points = 1000;
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return (str_ends_with($this->email, '@bruler.ru') && $this->hasVerifiedEmail(
                )) || ($this->email === '0870989@gmail.com' || $this->email === 'ya@sanek.dev');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'city',
        'birthday',
        'phone',
        'email',
        'password',
        'params',
        'vk_id',
        'telegram_id',
        'yandex_id',
        'image',
        'points'
    ];

    /**
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
        'params' => 'json',
    ];

    public function favourites()
    {
        return $this->belongsToMany(Product::class, 'favourites');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cashback()
    {
        $sumOrders = 0;
        foreach ($this->orders as $order) {
            if ($order->status != 'not_cofirmed' && $order->status != 'created') {
                $sumOrders += $order->price;
            }
        }

        if ($sumOrders > 149999) {
            return 7;
        }

        if ($sumOrders > 49999 && $sumOrders <= 149999) {
            return 5;
        }

        return 3;
    }
}
