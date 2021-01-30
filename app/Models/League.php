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
        return $this->hasMany(Game::class)->where('home_id', $club_id)->get();
    }

    public function getAwayMatches($club_id)
    {
        return $this->hasMany(Game::class)->where('away_id', $club_id)->get();
    }

    public function setClubs()
    {
        $rand_keys = array_rand($this->clubs, 4);
        for ($i = 0; $i < 4; $i++) {
            $club = new Club();
            $club->league_id = $this->id;
            $club->name = $this->clubs[$rand_keys[$i]];
            $club->percent = rand(0, 100);
            $club->save();
        }
    }

    public function setGames()
    {
        $clubs = Club::select('id')
            ->where('league_id', $this->id)
            ->get()
            ->pluck('id')
            ->toArray();
        $tour = 1;
        $numberOfClubs = count($clubs);
        for ($i = 0; $i < $numberOfClubs - 1; $i++) {
            for ($round = 0; $round < $numberOfClubs / 2; $round++) {
                $rounds[$i][] = [$clubs[$round], $clubs[$numberOfClubs - 1 - $round]];
            }
            //dd($clubs);
            $clubs[] = array_splice($clubs, 1, 1)[0];
            //dd($clubs);
        }
        $clubs[] = array_splice($clubs, 1, 1)[0];
        foreach ($rounds as $round) {
            foreach ($round as $game) {
                $this->saveGames($game[0], $game[1], $tour);
            }
            foreach ($round as $game) {
                $this->saveGames($game[1], $game[0], $tour + 3);
            }
            $tour++;
        }
    }

    public function saveGames($home, $away, $tour)
    {
        try {
            $game = new Game();
            $game->home_id = $home;
            $game->away_id = $away;
            $game->league_id = $this->id;
            $game->tour = $tour;
            $game->save();
            //dd($game);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /** Game Table */
    public function updateClubPoints()
    {
        // dd($this->leagueClubs);
        foreach ($this->leagueClubs as $club) {
            $club->win = $this->getTotalWin($club->id);
            $club->draw = $this->getTotalDraw($club->id);
            $club->lost = $this->getTotalLost($club->id);
            $club->gf = $this->getTotalGF($club->id);
            $club->ga = $this->getTotalGA($club->id);
            $club->gd = $this->getTotalGD($club->id);
            $club->points = $this->getTotalPoint($club->id);
            $club->save();
        }
    }

    public function getTotalWin($club_id)
    {
        $result = 0;
        $league = League::latest()->first();
        $homeMatches = $league->getHomeMatches($club_id);
        $awayMatches = $league->getAwayMatches($club_id);

        foreach ($homeMatches as $match) {
            if ($match->home_goal_count > $match->away_goal_count) {
                $result++;
            }
        }
        foreach ($awayMatches as $match) {
            if ($match->away_goal_count > $match->home_goal_count) {
                $result++;
            }
        }

        return $result;
    }

    public function getTotalDraw($club_id)
    {
        $result = 0;
        $league = League::latest()->first();
        $homeMatches = $league->getHomeMatches($club_id);
        $awayMatches = $league->getAwayMatches($club_id);

        foreach ($homeMatches as $match) {
            if ($match->home_goal_count == $match->away_goal_count && $match->played_at) {
                $result++;
            }
        }
        foreach ($awayMatches as $match) {
            if ($match->away_goal_count == $match->home_goal_count && $match->played_at) {
                $result++;
            }
        }

        return $result;
    }

    public function getTotalLost($club_id)
    {
        $result = 0;
        $league = League::latest()->first();
        $homeMatches = $league->getHomeMatches($club_id);
        $awayMatches = $league->getAwayMatches($club_id);

        foreach ($homeMatches as $match) {
            if ($match->home_goal_count < $match->away_goal_count) {
                $result++;
            }
        }
        foreach ($awayMatches as $match) {
            if ($match->away_goal_count < $match->home_goal_count) {
                $result++;
            }
        }

        return $result;
    }

    public function getTotalPoint($club_id)
    {
        return $this->getTotalWin($club_id) * 3 + $this->getTotalDraw($club_id) * 1;
    }

    public function getTotalGF($club_id)
    {
        $result = 0;
        $league = League::latest()->first();
        $homeMatches = $league->getHomeMatches($club_id);
        $awayMatches = $league->getAwayMatches($club_id);

        foreach ($homeMatches as $match) {
            $result += $match->home_goal_count;
        }
        foreach ($awayMatches as $match) {
            $result += $match->away_goal_count;
        }

        return $result;
    }

    public function getTotalGA($club_id)
    {
        $result = 0;
        $league = League::latest()->first();
        $homeMatches = $league->getHomeMatches($club_id);
        $awayMatches = $league->getAwayMatches($club_id);

        foreach ($homeMatches as $match) {
            $result += $match->away_goal_count;
        }
        foreach ($awayMatches as $match) {
            $result += $match->home_goal_count;
        }

        return $result;
    }

    public function getTotalGD($club_id)
    {
        $totalGF = $this->getTotalGF($club_id);
        $totalGA = $this->getTotalGA($club_id);
        $result = $totalGF - $totalGA;

        return $result;
    }

    public function getTotalPointsAttribute()
    {
        $points = 0;
        foreach ($this->leagueClubs as $club) {
            $points += $this->getTotalPoint($club->id);
        }
        return $points;
    }
}
