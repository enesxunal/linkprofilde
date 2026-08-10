<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];

    public function qrcodes()
    {
        return $this->hasMany(QRCode::class);
    }

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }

    public function pricing_plan()
    {
        return $this->belongsTo(PricingPlan::class);
    }

    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Şüpheli hesaplar: e-posta doğrulanmamış veya isim rastgele görünüyor (tek kelime 20+ karakter).
     */
    public function scopeSuspicious($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('email_verified_at')
                ->orWhereRaw("(LENGTH(TRIM(name)) > 20 AND TRIM(name) NOT LIKE '% %')");
        });
    }
}
