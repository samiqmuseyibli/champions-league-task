<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Game;
use App\Models\League;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GameController extends Controller
{
    private $model;
    public function __construct(Game $match)
    {
        $this->model = $match;
    }

    public function index(Request $request)
    {
        $league = League::with([
            'leagueClubs' => function ($query) {
                return $query->orderByDesc('points')->orderByDesc('gd');
            }
        ])->latest()
            ->first();

        //return $league;
        if ($league == null) {
            return redirect()->route('new');
        }

        $games = $this->model
            ->with(['homeClub', 'awayClub'])
            ->where('league_id', $league->id)
            ->orderBy('tour')
            ->get();
        //dd($games);
        $tour = 0;
        if ($request->get('tour')) {
            $tour = $request->get('tour');
        }
        return view('table', compact('games', 'league', 'tour'));
    }

    public function game(Request $request)
    {
        $league = League::with(['leagueClubs'])->latest()->first();
        if ($league == null) return false;

        //if clicked "Play all games" - button
        if ($request->tour == "all") {
            for ($tour = 1; $tour <= 6; $tour++) {
                $games = $this->model->with(['homeClub', 'awayClub'])->where('league_id', $league->id)->where('tour', $tour)->get();
                $this->playGames($games);
            }
        } else {
            //if clicked "Next tour" - button
            $tour = $request->tour + 1;
            $games = $this->model->with(['homeClub', 'awayClub'])->where('league_id', $league->id)->where('tour', $tour)->get();
            $this->playGames($games);
        }
        $league->updateClubPoints();
        return redirect()->route('table', ['tour' => $tour ?? '']);
    }

    public function playGames($games)
    {
        foreach ($games as $game) {
            if ($game->played_at == null) {
                $homeGoals = $this->createHomeScore($game->homeClub->percent, true);
                $awayGoals = $this->createAwayScore($game->awayClub->percent, false);
                $this->updateGameResult($game->id, $homeGoals, $awayGoals);
            }
        }
        return true;
    }

    public function updateGameResult($id, $homeGoals, $awayGoals)
    {
        $game = $this->model->find($id);
        $game->played_at = now();
        $game->home_goal_count = $homeGoals;
        $game->away_goal_count = $awayGoals;
        $game->save();
        return true;
    }

    public function createHomeScore($percent)
    {
        //if home change will be high than away
        return intval(($percent + rand(1, 20)) / 20);
    }

    public function createAwayScore($percent)
    {
        //if away chance will be high than away
        return intval(($percent + rand(1, 15)) / 20);
    }

    //Change Game score
    public function changeGameScore(Request $request)
    {
        //return response()->json($request->all());
        try {
            $game = $this->model->find($request->input('game'));
            $game->home_goal_count = $request->input('home');
            $game->away_goal_count = $request->input('away');
            $game->save();
            $game->league->updateClubPoints();
            return response()->json(['success' => true, 'message' => 'successfully updated'], Response::HTTP_ACCEPTED);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
