<?php

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

Route::middleware('user-auth')->group(function () {

    // Users endpoints
    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'getAllUsers');

        Route::get('/users/relationships', 'getRelationships');
        Route::put('/users/relationships/{id}', 'updateRelationship');

        Route::get('/users/me', 'refreshToken');

        Route::get('/users/{id}', 'getOneUser');

        
    });

    // Squad and squad members endpoints
    Route::controller(SquadController::class)->group(function () {
        Route::post('/squads', 'create')->middleware('roles:admin,coach');
        Route::get('/squads', 'getAllSquads');
        Route::get('/squads/{id}', 'getOneSquad');
        Route::put('/squads/{id}', 'updateSquad')->middleware('roles:admin,coach');
        Route::delete('/squads/{id}', 'deleteSquad')->middleware('roles:admin');

        Route::post('/squads/{id}/members', 'addSwimmersToSquad')->middleware('roles:admin,coach');
        Route::get('/squads/{id}/members', 'getSquadMembers');
        Route::get('/squads-members', 'getAllSquadMembers');
        Route::delete('/squads/{id}/members/{memberId}', 'removeSquadMember')->middleware('roles:admin,coach');
    });

    // Trainings endpoints
    Route::controller(TrainingController::class)->group(function () {
        Route::post('/trainings', 'create')->middleware('roles:admin,coach');
        Route::get('/trainings', 'getAllTrainings');
        Route::get('/trainings/{id}', 'getOneTraining');
        Route::put('/trainings/{id}', 'updateTraining')->middleware('roles:admin,coach');
        Route::delete('/trainings/{id}', 'deleteTraining')->middleware('roles:admin');

        Route::post('/training-performances', 'addTrainingPerformance')->middleware('roles:admin,coach');
        Route::put('/training-performances/{performanceId}', 'updateTrainingPerformance')->middleware('roles:admin,coach');
        Route::get('/training-performances', 'getTrainingPerformances');
        Route::delete('/training-performances/{performanceId}', 'deleteTrainingPerformance')->middleware('roles:admin,coach');
    });

});

