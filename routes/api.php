<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SquadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::controller(UserController::class)->group(function () {
    Route::post('/users', 'signup');
    Route::post('/signin', 'signin');
});

Route::middleware('user-auth')->group(function () {

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'getAllUsers');
        Route::get('/users/{id}', 'getOneUser');
    });

    Route::controller(SquadController::class)->group(function () {
        Route::post('/squads', 'create')->middleware('roles:admin,coach');
        Route::get('/squads', 'getAllSquads');
        Route::get('/squads/{id}', 'getOneSquad');
        Route::put('/squads/{id}', 'updateSquad')->middleware('roles:admin,coach');
        Route::delete('/squads/{id}', 'deleteSquad')->middleware('roles:admin');

        Route::post('/squads/{id}/members', 'addSwimmersToSquad')->middleware('roles:admin,coach');
        Route::get('/squads/{id}/members', 'getSquadMembers')->middleware('roles:admin,coach');
    });

});
