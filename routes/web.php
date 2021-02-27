<?php

use App\Http\Controllers\FixtureController;
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

//Group
Route::get('/new', [LeagueController::class, 'index'])->name('new');
Route::post('/new', [LeagueController::class, 'create']);

Route::get('/table', [FixtureController::class, 'index'])->name('table');

//Group
Route::get('/fixture', [FixtureController::class, 'fixture'])->name('fixture');
Route::post('/fixture', [FixtureController::class, 'changefixtureScore']);
