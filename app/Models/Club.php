<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    public function totalGames(){
        return $this->hasMany(self::class)->where('home_id', $this->id)->orWhere('away_id', $this->id);
    }

    public function calculatePrediction(){
        return '';
    }
}
