<?php

namespace App\Http\Controllers;

use App\Models\League;
use Illuminate\Http\Request;

class LeagueController extends Controller
{
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
        $league->setClubs();
        $league->setGames();
        return response()->json(array('success' => true), 200);
    }
}
