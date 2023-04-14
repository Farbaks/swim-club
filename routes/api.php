<?php

use App\Http\Controllers\RaceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SquadController;
use App\Http\Controllers\TrainingController;

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

Route::get('/strokes', [TrainingController::class, 'getStrokes']);

Route::get('/swimmers', [UserController::class, 'getSwimmers']);
Route::get('/report', [UserController::class, 'getReport']);

Route::middleware('user-auth')->group(function () {

    // Users endpoints
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'getAllUsers');
        // 
        Route::post('/users/relationships', 'createWard');
        Route::get('/users/relationships', 'getRelationships');
        Route::put('/users/relationships/{id}/info', 'updateRelationshipInfo');
        // 
        Route::put('/users/password', 'updatePassword');
        // 
        Route::get('/users/me', 'refreshToken');
        Route::put('/users/me', 'updateOneUser');
        Route::get('/users/{id}', 'getOneUser');
    });

    // Squad and squad members endpoints
    Route::controller(SquadController::class)->group(function () {
        Route::post('/squads', 'create')->middleware('roles:admin,coach');
        Route::get('/squads', 'getAllSquads');
        Route::get('/squads/{id}', 'getOneSquad');
        Route::put('/squads/{id}', 'updateSquad')->middleware('roles:admin,coach');
        Route::delete('/squads/{id}', 'deleteSquad')->middleware('roles:admin');
        // 
        // Get all squad members without limit (for dropdowns)
        Route::get('/squads-members', 'getAllSquadMembers');
        // 
        Route::post('/squads/{id}/members', 'addSwimmersToSquad')->middleware('roles:admin,coach');
        Route::get('/squads/{id}/members', 'getSquadMembers');
        Route::delete('/squads/{id}/members/{memberId}', 'removeSquadMember')->middleware('roles:admin,coach');
    });

    // Trainings endpoints
    Route::controller(TrainingController::class)->group(function () {
        Route::post('/trainings', 'create')->middleware('roles:admin,coach');
        Route::get('/trainings', 'getAllTrainings');
        Route::get('/trainings/{id}', 'getOneTraining');
        Route::put('/trainings/{id}', 'updateTraining')->middleware('roles:admin,coach');
        Route::delete('/trainings/{id}', 'deleteTraining')->middleware('roles:admin');
        // 
        Route::post('/training-performances', 'addTrainingPerformance')->middleware('roles:admin,coach');
        Route::put('/training-performances/{performanceId}', 'updateTrainingPerformance')->middleware('roles:admin,coach');
        Route::get('/training-performances', 'getTrainingPerformances');
        Route::delete('/training-performances/{performanceId}', 'deleteTrainingPerformance')->middleware('roles:admin,coach');
    });

    // Gala(Race) endpoints
    Route::controller(RaceController::class)->group(function () {
        // Race
        Route::post('/galas', 'createRace')->middleware('roles:admin');
        Route::get('/galas', 'getRaces');
        Route::get('/all-galas', 'getAllRaces');
        Route::get('/galas/{id}', 'getOneRace');
        Route::put('/galas/{id}', 'updateRace')->middleware('roles:admin');
        Route::delete('/galas/{id}', 'deleteRace')->middleware('roles:admin');

        // Race Group
        Route::post('/gala-groups', 'createGroup')->middleware('roles:admin');
        Route::get('/galas/{id}/groups', 'getAllRaceGroups');
        Route::put('/gala-groups/{id}', 'updateRaceGroup')->middleware('roles:admin');
        Route::delete('/gala-groups/{id}', 'deleteRaceGroup')->middleware('roles:admin');

        // Race Group members
        Route::post('/gala-members', 'addRaceMember')->middleware('roles:admin');
        Route::put('/gala-members/{id}', 'updateRaceMember')->middleware('roles:admin');
        Route::delete('/gala-members/{id}', 'deleteRaceMember')->middleware('roles:admin');
    });

});