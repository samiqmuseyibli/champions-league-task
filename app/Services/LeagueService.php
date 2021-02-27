<?php

namespace App\Services;

use App\Models\Club;
use App\Models\Fixture;
use App\Models\League;
use App\Repositories\LeagueRepository;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class LeagueService
{
    /**
     * @var $leagueRepository
     */
    protected $league;


    /**
     * LeagueService constructor.
     *
     * @param League $league
     */
    public function __construct(League $league)
    {
        $this->league = $league;
    }


    public function setClubs(League $league)
    {
        $clubs = $league->getClubs();
        $rand_keys = array_rand($clubs, 4);
        for ($i = 0; $i < 4; $i++) {
            $club = new Club();
            $club->league_id = $league->id;
            $club->name = $clubs[$rand_keys[$i]];
            $club->percent = rand(0, 100);
            $club->save();
        }
    }

    public function setFixtures(League $league)
    {
        $clubs = Club::select('id')
            ->where('league_id', $league->id)
            ->get()
            ->pluck('id')
            ->toArray();
        $tour = 1;
        $numberOfClubs = count($clubs);
        for ($i = 0; $i < $numberOfClubs - 1; $i++) {
            for ($round = 0; $round < $numberOfClubs / 2; $round++) {
                $rounds[$i][] = [$clubs[$round], $clubs[$numberOfClubs - 1 - $round]];
            }
            $clubs[] = array_splice($clubs, 1, 1)[0];
        }
        foreach ($rounds as $round) {
            foreach ($round as $Fixture) {
                $this->saveFixtures($Fixture[0], $Fixture[1], $tour, $league);
            }
            foreach ($round as $Fixture) {
                $this->saveFixtures($Fixture[1], $Fixture[0], $tour + 3, $league);
            }
            $tour++;
        }
    }

    public function saveFixtures($home, $away, $tour, League $league)
    {
        try {
            $Fixture = new Fixture();
            $Fixture->home_id = $home;
            $Fixture->away_id = $away;
            $Fixture->league_id = $league->id;
            $Fixture->tour = $tour;
            $Fixture->save();
            //dd($Fixture);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }


    /** Fixture Table */
    public function updateClubPoints(League $league)
    {
        // dd($this->leagueClubs);
        foreach ($league->leagueClubs as $club) {
            $club->win = $this->getTotalWin($club->id, $league);
            $club->draw = $this->getTotalDraw($club->id, $league);
            $club->lost = $this->getTotalLost($club->id, $league);
            $club->gf = $this->getTotalGF($club->id, $league);
            $club->ga = $this->getTotalGA($club->id, $league);
            $club->gd = $this->getTotalGD($club->id, $league);
            $club->points = $this->getTotalPoint($club->id, $league);
            $club->save();
        }
    }

    public function getTotalWin($club_id, League $league)
    {
        $result = 0;
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

    public function getTotalDraw($club_id, League $league)
    {
        $result = 0;
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

    public function getTotalLost($club_id, League $league)
    {
        $result = 0;
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

    public function getTotalPoint($club_id, League $league)
    {
        return $this->getTotalWin($club_id, $league) * 3 + $this->getTotalDraw($club_id, $league) * 1;
    }

    public function getTotalGF($club_id, $league)
    {
        $result = 0;
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

    public function getTotalGA($club_id, League $league)
    {
        $result = 0;
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

    public function getTotalGD($club_id, League $league)
    {
        $totalGF = $this->getTotalGF($club_id, $league);
        $totalGA = $this->getTotalGA($club_id, $league);
        $result = $totalGF - $totalGA;

        return $result;
    }

    public function getTotalPointsAttribute(League $league)
    {
        $points = 0;
        foreach ($league->leagueClubs as $club) {
            $points += $this->getTotalPoint($club->id, $league);
        }
        return $points;
    }
}
