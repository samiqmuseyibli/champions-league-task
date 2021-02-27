<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    private $clubs =  [
        "Arsenal",
        "Aston Villa",
        "Blackburn Rovers",
        "Chelsea",
        "Coventry City",
        "Crystal Palace",
        "Everton",
        "Ipswich Town",
        "Leeds United",
        "Liverpool",
        "Manchester City",
        "Manchester United",
        "Middlesbrough",
        "Norwich City",
        "Nottingham Forest",
        "Oldham Athletic",
        "Queens Park Rangers",
        "Sheffield United",
        "Sheffield Wednesday",
        "Southampton",
        "Tottenham Hotspur",
        "Wimbledon",
    ];

    protected $appends = [
        'totalPoints'
    ];

    public function leagueClubs()
    {
        return $this->hasMany(Club::class, 'league_id');
    }

    public function getHomeMatches($club_id)
    {
        return $this->hasMany(Fixture::class)->where('home_id', $club_id)->get();
    }

    public function getAwayMatches($club_id)
    {
        return $this->hasMany(Fixture::class)->where('away_id', $club_id)->get();
    }

    public function getClubs(){
        return $this->clubs;
    }
}
