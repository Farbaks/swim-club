<?php

namespace App\Http\Controllers;

use App\Http\Requests\Trainings\NewTrainingDto;
use App\Http\Requests\Trainings\NewTrainingPerformanceDto;
use App\Http\Requests\Trainings\UpdateTrainingDto;
use App\Http\Requests\Trainings\UpdateTrainingPerformanceDto;
use App\Models\Squad;
use App\Models\SquadMember;
use App\Models\Stroke;
use App\Models\Training;
use App\Models\TrainingPerformance;
use App\Models\User;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    // Function to fetch trainings
    public function getStrokes(Request $request)
    {

        $strokes = Stroke::get();

        return response()->json([
            'status' => 200,
            'message' => 'Squads fetched successfully.',
            'data' => $strokes,
        ], 200);
    }

    //Function to create a training
    public function create(NewTrainingDto $request)
    {

        // Check if training with the same name already exists
        $checkTraining = Training::where('name', $request->name)->where('isDeleted', false)->first();

        if ($checkTraining != "") {
            return response()->json([
                'status' => 400,
                'message' => 'A training with this name already exists.',
                'data' => []
            ], 200);
        }

        // Check if squad exists
        $checkSquad = Squad::where('id', $request->squadId)->where('isDeleted', false)->first();

        if ($checkSquad == "") {
            return response()->json([
                'status' => 400,
                'message' => 'The provided squad does not exist.',
                'data' => []
            ], 200);
        }

        $training = new Training;
        $training->name = $request->name;
        $training->description = $request->description;
        $training->requirements = $request->requirements;
        $training->startTime = $request->startTime;
        $training->endTime = $request->endTime;
        $training->day = $request->day;
        $training->interval = $request->interval;
        $training->squadId = $request->squadId;

        $training->save();

        return response()->json([
            'status' => 201,
            'message' => 'Training created successfully.',
            'data' => $training
        ], 201);

    }

    // Function to fetch trainings
    public function getAllTrainings(Request $request)
    {

        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;
        $squad = $request->has('squad') ? $request->get('squad') : '';
        $search = $request->has('query') ? $request->get('query') : '';

        $trainings = Training::with('squad')
            ->offset(($page - 1) * $limit)->limit($limit)
            ->join('squads', 'trainings.squadId', '=', 'squads.id')
            ->where(function ($query) use ($search) {
                $query->where('trainings.name', 'like', '%' . $search . '%')
                    ->orWhere('trainings.requirements', 'like', '%' . $search . '%')
                    ->orWhere('trainings.description', 'like', '%' . $search . '%');
            })
            ->where('squads.name', 'like', '%' . $squad . '%')
            ->where('trainings.isDeleted', false)
            ->select('trainings.*')
            ->orderBy('created_at', 'desc')
            ->get();

        $trainingsCount = Training::join('squads', 'trainings.squadId', '=', 'squads.id')
            ->where(function ($query) use ($search) {
                $query->where('trainings.name', 'like', '%' . $search . '%')
                    ->orWhere('trainings.requirements', 'like', '%' . $search . '%')
                    ->orWhere('trainings.description', 'like', '%' . $search . '%');
            })
            ->where('squads.name', 'like', '%' . $squad . '%')
            ->where('trainings.isDeleted', false)
            ->orderBy('created_at', 'desc')
            ->count();

        return response()->json([
            'status' => 200,
            'message' => 'Trainings fetched successfully.',
            'data' => $trainings,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $trainingsCount
            ]
        ], 200);
    }

    // Function to fetch one training
    public function getOneTraining(Request $request, string $id)
    {
        $training = Training::with('squad')->where('id', $id)->where('isDeleted', false)->first();

        if ($training == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This training does not exist',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Training details fetched successfully.',
            'data' => $training
        ], 200);
    }

    // Functon to update training
    public function updateTraining(UpdateTrainingDto $request, string $id)
    {
        $training = Training::where('id', $id)->where('isDeleted', false)->first();

        if ($training == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This training does not exist',
                'data' => []
            ], 200);
        }

        if ($request->squadId) {
            $checkSquad = Squad::where('id', $request->squadId)->where('isDeleted', false)->first();

            if ($checkSquad == "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'Squad does not exist.',
                    'data' => []
                ], 200);
            }
        }


        if ($request->name)
            $training->name = $request->name;
        if ($request->description)
            $training->description = $request->description;
        if ($request->requirements)
            $training->requirements = $request->requirements;
        if ($request->startTime)
            $training->startTime = $request->startTime;
        if ($request->endTime)
            $training->endTime = $request->endTime;
        if ($request->day)
            $training->day = $request->day;
        if ($request->interval)
            $training->interval = $request->interval;
        if ($request->squadId)
            $training->squadId = $request->squadId;

        $training->save();

        return response()->json([
            'status' => 200,
            'message' => 'Training updated successfully.',
            'data' => $training
        ], 200);

    }

    // Function to delete training
    public function deleteTraining(Request $request, string $id)
    {
        $training = Training::where('id', $id)->where('isDeleted', false)->first();

        if ($training == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This training does not exist',
                'data' => []
            ], 200);
        }

        // Delete training (Hard delete)
        $training->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Training deleted successfully.',
            'data' => []
        ], 200);
    }

    // Function to add training performance
    public function addTrainingPerformance(NewTrainingPerformanceDto $request)
    {
        // Check if training exists
        $training = Training::where('id', $request->trainingId)->where('isDeleted', false)->first();

        if ($training == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This training does not exist',
                'data' => []
            ], 200);
        }

        // Check if member exists
        $checkSwimmer = SquadMember::where('id', $request->squadMemberId)->where('isDeleted', false)->first();

        if ($checkSwimmer == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid squad member id provided.',
                'data' => []
            ], 200);
        }

        // Check if stroke exists
        $checkStroke = Stroke::where('id', $request->strokeId)->first();

        if ($checkStroke == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid stroke id provided.',
                'data' => []
            ], 200);
        }

        $performance = new TrainingPerformance;

        $performance->trainingId = $request->trainingId;
        $performance->squadMemberId = $request->squadMemberId;
        $performance->time = $request->time;
        $performance->strokeId = $request->strokeId;
        $performance->rank = $request->rank;
        $performance->points = $request->points;
        $performance->trainingDate = date("Y-m-d", strtotime($request->trainingDate));

        $performance->save();

        return response()->json([
            'status' => 201,
            'message' => 'Training performance added successfully.',
            'data' => $performance
        ], 201);
    }

    // Function to fetch performance
    public function getTrainingPerformances(Request $request)
    {

        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $stroke = $request->has('stroke') ? $request->get('stroke') : '';
        $training = $request->has('training') ? $request->get('training') : '';
        $squad = $request->has('squad') ? $request->get('squad') : '';
        $search = $request->has('query') ? $request->get('query') : '';

        $performances = TrainingPerformance::with(['squadMember.squad', 'squadMember.user', 'training', 'stroke'])
            ->offset(($page - 1) * $limit)->limit($limit)
            ->join('squad_members', 'training_performances.squadMemberid', '=', 'squad_members.id')
            ->join('users', 'squad_members.userId', '=', 'users.id')
            ->join('squads', 'squad_members.squadId', '=', 'squads.id')
            ->join('trainings', 'training_performances.trainingid', '=', 'trainings.id')
            ->join('strokes', 'training_performances.strokeId', '=', 'strokes.id')
            ->where(function ($query) use ($search) {
                $query->where('users.firstName', 'like', '%' . $search . '%')
                    ->orWhere('users.lastName', 'like', '%' . $search . '%')
                    ->orWhere('strokes.name', 'like', '%' . $search . '%')
                    ->orWhere('trainings.name', 'like', '%' . $search . '%');
            })
            ->where('strokes.name', 'like', '%' . $stroke . '%')
            ->where('trainings.name', 'like', '%' . $training . '%')
            ->where('squads.name', 'like', '%' . $squad . '%')
            ->select('training_performances.*')
            ->orderBy('created_at', 'desc')
            ->get();

        $performancesCount = TrainingPerformance::join('squad_members', 'training_performances.squadMemberid', '=', 'squad_members.id')
            ->join('users', 'squad_members.userId', '=', 'users.id')
            ->join('trainings', 'training_performances.trainingid', '=', 'trainings.id')
            ->join('strokes', 'training_performances.strokeId', '=', 'strokes.id')
            ->where(function ($query) use ($search) {
                $query->where('users.firstName', 'like', '%' . $search . '%')
                    ->orWhere('users.lastName', 'like', '%' . $search . '%')
                    ->orWhere('strokes.name', 'like', '%' . $search . '%')
                    ->orWhere('trainings.name', 'like', '%' . $search . '%');
            })
            ->where('strokes.name', 'like', '%' . $stroke . '%')
            ->where('trainings.name', 'like', '%' . $training . '%')
            ->orderBy('created_at', 'desc')
            ->count();

        return response()->json([
            'status' => 200,
            'message' => 'Training Performances fetched successfully.',
            'data' => $performances,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $performancesCount
            ]
        ], 200);
    }

    // Function to edit training performance
    public function updateTrainingPerformance(UpdateTrainingPerformanceDto $request, string $performanceId)
    {
        // Check if training exists
        $training = Training::where('id', $request->trainingId)->where('isDeleted', false)->first();

        if ($training == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This training does not exist',
                'data' => []
            ], 200);
        }

        // Check if performance exists
        $performance = TrainingPerformance::where('id', $performanceId)->where('isDeleted', false)->first();

        if ($performance == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Training performance does not exist..',
                'data' => []
            ], 200);
        }

        if ($request->squadMemberId) {
            // Check if member exists
            $checkSwimmer = SquadMember::where('id', $request->squadMemberId)->where('isDeleted', false)->first();

            if ($checkSwimmer == "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid squad member id provided.',
                    'data' => []
                ], 200);
            }
        }

        if ($request->strokeId) {
            // Check if stroke exists
            $checkStroke = Stroke::where('id', $request->strokeId)->first();

            if ($checkStroke == "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'Invalid stroke id provided.',
                    'data' => []
                ], 200);
            }
        }

        if ($request->squadMemberId)
            $performance->squadMemberId = $request->squadMemberId;
        if ($request->time)
            $performance->time = $request->time;
        if ($request->strokeId)
            $performance->strokeId = $request->strokeId;
        if ($request->trainingId)
            $performance->trainingId = $request->trainingId;
        if ($request->rank)
            $performance->rank = $request->rank;
        if ($request->points)
            $performance->points = $request->points;
        if ($request->trainingDate)
            $performance->trainingDate = date("Y-m-d", strtotime($request->trainingDate));

        $performance->save();

        return response()->json([
            'status' => 201,
            'message' => 'Training performance updated successfully.',
            'data' => $performance
        ], 201);
    }

    public function deleteTrainingPerformance(Request $request, string $trainingId, string $performanceId)
    {
        // Check if training exists
        $training = Training::where('id', $trainingId)->where('isDeleted', false)->first();

        if ($training == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This training does not exist',
                'data' => []
            ], 200);
        }

        // Check if performance exists
        $checkPerformance = TrainingPerformance::where('id', $performanceId)->where('isDeleted', false)->first();

        if ($checkPerformance == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Training performance record does not exist..',
                'data' => []
            ], 200);
        }

        // Delete training performance (Hard delete)
        $checkPerformance->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Training performance deleted successfully.',
            'data' => []
        ], 200);
    }
}