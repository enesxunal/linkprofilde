<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function qrcode(){
        return $this->belongsTo(QRCode::class);
    }

    public function theme(){
        return $this->belongsTo(Theme::class);
    }

    public function custom_theme(){
        return $this->belongsTo(CustomTheme::class);
    }

    public function items(){
        return $this->hasMany(LinkItem::class)->orderBy('item_position', 'asc');
    }

    public function visited(){
        return $this->hasMany(ShetabitVisit::class);
    }
}
