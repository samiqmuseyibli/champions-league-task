<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    public function homeClub() {
        return $this->belongsTo(Club::class, 'home_id');
    }

    public function awayClub() {
        return $this->belongsTo(Club::class, 'away_id');
    }

    public function league(){
        return $this->belongsTo(League::class);
    }
}
