<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shetabit\Visitor\Traits\Visitable;

class ShetabitVisit extends Model
{
    use HasFactory, Visitable;

    public function link(){
        return $this->belongsTo(Link::class);
    }
}
