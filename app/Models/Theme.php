<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'background',
        'text_color',
        'button_style',
        'font_family',
        'theme_demo',
        'bg_image',
        'type',
    ];

    public function link(){
        return $this->belongsTo(Link::class);
    }
}
