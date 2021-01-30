<?php

use App\Http\Controllers\ClubController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LeagueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LeagueController::class, 'index'])->name('home');

Route::get('/new', [LeagueController::class, 'index'])->name('new');
Route::post('/new', [LeagueController::class, 'create']);

Route::get('/table', [GameController::class, 'index'])->name('table');
Route::get('/game', [GameController::class, 'game'])->name('game');
Route::post('/game', [GameController::class, 'changeGameScore']);
