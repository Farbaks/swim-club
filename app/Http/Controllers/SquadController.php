<?php

namespace App\Http\Controllers;

use App\Http\Requests\Squads\AddSwimmersToSquadDto;
use App\Http\Requests\Squads\NewSquadDto;
use App\Http\Requests\Squads\RemoveSwimmerFromSquadDto;
use App\Http\Requests\Squads\UpdateSquadDto;
use App\Models\Squad;
use App\Models\SquadMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SquadController extends Controller
{
    //Function to create a squad
    public function create(NewSquadDto $request)
    {
        // Check if squad with name already exists
        $checkSquad = Squad::where('name', $request->name)->where('isDeleted', false)->first();

        if ($checkSquad != "") {
            return response()->json([
                'status' => 400,
                'message' => 'A squad with this name already exists',
                'data' => []
            ], 200);
        }

        // Check if coach is valid
        $checkCoach = User::where('id', $request->coachId)
            ->where('role', 'coach')->where('isDeleted', false)->first();

        if ($checkCoach == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid coach id provided.',
                'data' => []
            ], 200);
        }

        $squad = new Squad;
        $squad->name = $request->name;
        $squad->description = $request->description;
        $squad->rank = $request->rank ?? '';
        $squad->coachId = $request->coachId;
        $squad->save();

        return response()->json([
            'status' => 201,
            'message' => 'Squad created successfully.',
            'data' => $squad
        ], 201);

    }

    // Function to fetch squads
    public function getAllSquads(Request $request)
    {

        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $search = $request->has('query') ? $request->get('query') : '';

        $squads = Squad::with('coach')
            ->offset(($page - 1) * $limit)->limit($limit)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })->where('isDeleted', false)->get();

        $squadsCount = Squad::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        })->where('isDeleted', false)->count();

        return response()->json([
            'status' => 200,
            'message' => 'Squads fetched successfully.',
            'data' => $squads,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $squadsCount
            ]
        ], 200);
    }

    // Function to fetch one squad
    public function getOneSquad(Request $request, string $id)
    {
        $squad = Squad::where('id', $id)->where('isDeleted', false)->first();

        if ($squad == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This squad does not exist',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Squad details fetched successfully.',
            'data' => $squad
        ], 200);
    }
    
    // Functon to update squad
    public function updateSquad(UpdateSquadDto $request, string $id)
    {
        $squad = Squad::where('id', $id)->where('isDeleted', false)->first();

        if ($squad == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This squad does not exist',
                'data' => []
            ], 200);
        }

        $checkUser = Squad::where('name', $request->name)->where('isDeleted', false)->first();

        if ($checkUser != "") {
            return response()->json([
                'status' => 400,
                'message' => 'A squad with this name already exists',
                'data' => []
            ], 200);
        }

        if ($request->name)
            $squad->name = $request->name;
        if ($request->description)
            $squad->description = $request->description;
        $squad->rank = $request->rank;

        $squad->save();

        return response()->json([
            'status' => 200,
            'message' => 'Squad updated successfully.',
            'data' => $squad
        ], 200);

    }

    // Function to delete squad
    public function deleteSquad(Request $request, string $id)
    {
        $squad = Squad::where('id', $id)->where('isDeleted', false)->first();

        // Log::channel('stderr')->debug($squad);

        if ($squad == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This squad does not exist',
                'data' => []
            ], 200);
        }

        // Delete squad (Hard delete)
        $squad->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Squad deleted successfully.',
            'data' => []
        ], 200);
    }

    // Function to add swimmers to squad
    public function addSwimmersToSquad(AddSwimmersToSquadDto $request, string $id)
    {
        // Check if squad exists
        $squad = Squad::where('id', $id)->where('isDeleted', false)->first();

        if ($squad == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This squad does not exist',
                'data' => []
            ], 200);
        }

        // Check if swimmer is valid
        $checkSwimmer = User::where('id', $request->swimmer)
            ->where('role', 'swimmer')->where('isDeleted', false)->first();

        if ($checkSwimmer == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Invalid swimmer id provided.',
                'data' => []
            ], 200);
        }

        // Check if user has not yet been added to squad
        $check = SquadMember::where('squadId', $id)
            ->where('userId', $request->swimmer)->where('isDeleted', false)->first();

        if ($check) {
            return response()->json([
                'status' => 400,
                'message' => 'This user is already a part of the squad.',
                'data' => []
            ], 200);
        }

        $newMember = new SquadMember;

            $newMember->squadId = $id;
            $newMember->userId = $request->swimmer;

            $newMember->save();

        return response()->json([
            'status' => 201,
            'message' => 'Squad member(s) added successfully.',
            'data' => []
        ], 201);
    }

    // Function to fetch sqaud members
    public function getSquadMembers(Request $request, string $id)
    {
        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $search = $request->has('query') ? $request->get('query') : '';

        $members = SquadMember::offset(($page - 1) * $limit)->limit($limit)
            ->join('users', 'squad_members.userId', '=', 'users.id')
            ->where(function ($query) use ($search) {
                $query->where('users.firstName', 'like', '%' . $search . '%')
                    ->orWhere('users.lastName', 'like', '%' . $search . '%');
            })
            ->where('users.isDeleted', false)
            ->where('squad_members.isDeleted', false)
            ->select('users.*')
            ->get();

        $membersCount = SquadMember::join('users', 'squad_members.userId', '=', 'users.id')
            ->where(function ($query) use ($search) {
                $query->where('users.firstName', 'like', '%' . $search . '%')
                    ->orWhere('users.lastName', 'like', '%' . $search . '%');
            })
            ->where('users.isDeleted', false)
            ->where('squad_members.isDeleted', false)
            ->count();

        return response()->json([
            'status' => 200,
            'message' => 'Squad Members fetched successfully.',
            'data' => $members,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $membersCount
            ]
        ], 200);
    }

    // Function to remove swimmer from squad
    public function removeSquadMember(RemoveSwimmerFromSquadDto $request, string $id)
    {
        // Check if squad exists
        $squad = Squad::where('id', $id)->where('isDeleted', false)->first();

        if ($squad == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This squad does not exist',
                'data' => []
            ], 200);
        }

        // Check if swimmer is a member of squad
        $member = SquadMember::where('squadId', $id)
            ->where('userId', $request->swimmer)->where('isDeleted', false)->first();

        if ($member == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This member does not belong to this squad.',
                'data' => []
            ], 200);
        }


        // Delete squad member (Hard delete)
        $member->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Squad member deleted successfully.',
            'data' => []
        ], 200);

    }
}