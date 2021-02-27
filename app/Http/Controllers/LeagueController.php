<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Services\LeagueService;
use Illuminate\Http\Request;

class LeagueController extends Controller
{

    private $leagueService;

    public function __construct(LeagueService $leagueService)
    {
        $this->leagueService = $leagueService;
    }

    public function index()
    {
        return view('new');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(League $league)
    {
        $league->name = 'England Premier League';
        $league->save();
        $this->leagueService->setClubs($league);
        $this->leagueService->setFixtures($league);
        return response()->json(array('success' => true), 200);
    }
}
