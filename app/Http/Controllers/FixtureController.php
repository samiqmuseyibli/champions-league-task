<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Fixture;
use App\Models\League;
use App\Services\LeagueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FixtureController extends Controller
{
    private $model;
    public function __construct(Fixture $fixture)
    {
        $this->model = $fixture;
    }

    /**
     * Index
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        $tour = 0;
        if ($request->get('tour')) {
            $tour = $request->get('tour');
        }

        $league = League::with([
            'leagueClubs' => function ($query) {
                return $query->orderByDesc('points')->orderByDesc('gd');
            }
        ])->latest()
            ->first();

        if ($league == null) {
            return redirect()->route('new');
        }

        $fixtures = $this->model
            ->with(['homeClub', 'awayClub'])
            ->where('league_id', $league->id)
            ->orderBy('tour')
            ->get();

        return view('table', compact('fixtures', 'league', 'tour'));
    }

    public function fixture(Request $request, LeagueService $leagueService)
    {
        $league = League::with(['leagueClubs'])->latest()->first();
        if ($league == null) return false;

        //if clicked "Play all Fixtures" - button
        if ($request->tour == "all") {
            for ($tour = 1; $tour <= 6; $tour++) {
                $fixtures = $this->model->with(['homeClub', 'awayClub'])->where('league_id', $league->id)->where('tour', $tour)->get();
                $this->playFixtures($fixtures);
            }
        } else {
            //if clicked "Next tour" - button
            $tour = $request->tour + 1;
            $fixtures = $this->model->with(['homeClub', 'awayClub'])->where('league_id', $league->id)->where('tour', $tour)->get();
            $this->playFixtures($fixtures);
        }
        $leagueService->updateClubPoints($league);
        return redirect()->route('table', ['tour' => $tour ?? '']);
    }

    public function playFixtures($fixtures)
    {
        foreach ($fixtures as $fixture) {
            if ($fixture->played_at == null) {
                $homeGoals = $this->createHomeScore($fixture->homeClub->percent, true);
                $awayGoals = $this->createAwayScore($fixture->awayClub->percent, false);
                $this->updateFixtureResult($fixture->id, $homeGoals, $awayGoals);
            }
        }
        return true;
    }

    public function updateFixtureResult($id, $homeGoals, $awayGoals)
    {
        $fixture = $this->model->find($id);
        $fixture->played_at = now();
        $fixture->home_goal_count = $homeGoals;
        $fixture->away_goal_count = $awayGoals;
        $fixture->save();
        return true;
    }

    /**
     * Create home score
     *
     * @param int $percent
     * @return int $score
     * @throws \Exception
     */
    public function createHomeScore($percent): int
    {
        //if home change will be high than away
        return intval(($percent + rand(1, 20)) / 20);
    }

    /**
     * Create away score
     *
     * @param int $percent
     * @return int $score
     * @throws \Exception
     */
    public function createAwayScore($percent): int
    {
        return intval(($percent + rand(1, 15)) / 20);
    }

    /**
     * Change Fixture score
     *
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function changeFixtureScore(Request $request, LeagueService $leagueService): JsonResponse
    {
        try {
            $fixture = $this->model->find($request->input('fixture'));
            if ($fixture->home_goal_count != null && $fixture->away_goal_count != null) {
                $fixture->home_goal_count = $request->input('home');
                $fixture->away_goal_count = $request->input('away');
                $fixture->save();
                $leagueService->updateClubPoints($fixture->league);
                return response()->json(['success' => true, 'message' => 'successfully updated'], Response::HTTP_ACCEPTED);
            }
            return response()->json(['success' => false, 'message' => 'game not played yet'], Response::HTTP_BAD_GATEWAY);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
