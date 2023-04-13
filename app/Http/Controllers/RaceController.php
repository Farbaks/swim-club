<?php

namespace App\Http\Controllers;

use App\Http\Requests\Races\NewRaceDto;
use App\Http\Requests\Races\NewRaceGroupDto;
use App\Http\Requests\Races\NewRaceGroupMemberDto;
use App\Http\Requests\Races\UpdateRaceDto;
use App\Http\Requests\Races\UpdateRaceGroupDto;
use App\Http\Requests\Races\UpdateRaceGroupMemberDto;
use App\Models\Race;
use App\Models\RaceGroup;
use App\Models\RacePerformance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RaceController extends Controller
{
    // Function to create a race
    public function createRace(NewRaceDto $request)
    {
        // Check if race with name already exists
        $checkRace = Race::where('name', $request->name)->where('isDeleted', false)->first();

        if ($checkRace != "") {
            return response()->json([
                'status' => 400,
                'message' => 'A gala event with this name already exists',
                'data' => []
            ], 200);
        }

        $race = new Race;
        $race->name = $request->name;
        $race->description = $request->description;
        $race->requirements = $request->requirements;
        $race->startDate = $request->startDate;
        $race->endDate = $request->endDate;
        $race->save();

        return response()->json([
            'status' => 201,
            'message' => 'Gala event created successfully.',
            'data' => $race
        ], 201);
    }

    // Function to get all races
    public function getRaces(Request $request)
    {
        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $search = $request->has('query') ? $request->get('query') : '';

        $races = Race::offset(($page - 1) * $limit)->limit($limit)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('requirements', 'like', '%' . $search . '%');
            })->where('isDeleted', false)->orderBy('created_at', 'desc')->get();

        $racesCount = Race::where(function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('requirements', 'like', '%' . $search . '%');
        })->where('isDeleted', false)->orderBy('created_at', 'desc')->count();

        // Add group count
        foreach ($races as $race) {
            $membersCount = RaceGroup::where('raceId', $race->id)
                ->where('isDeleted', false)
                ->count();
            $race['membersCount'] = $membersCount;
        }

        return response()->json([
            'status' => 200,
            'message' => 'Gala events fetched successfully.',
            'data' => $races,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $racesCount
            ]
        ], 200);
    }

    // Function to get all races
    public function getAllRaces(Request $request)
    {

        $races = Race::orderBy('created_at', 'desc')->get();


        return response()->json([
            'status' => 200,
            'message' => 'Gala events fetched successfully.',
            'data' => $races,
        ], 200);
    }

    // Function to get one race
    public function getOneRace(Request $request, string $id)
    {
        $race = Race::with(['raceGroups.raceMembers' => function ($q){
            $q->orderBy('points', 'DESC');
        }])
        ->where('id', $id)->where('isDeleted', false)->first();

        if ($race == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This event does not exist',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Gala details fetched successfully.',
            'data' => $race
        ], 200);
    }

    // Function to update race
    public function updateRace(UpdateRaceDto $request, string $id)
    {
        $race = Race::where('id', $id)->where('isDeleted', false)->first();

        if ($race == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This event does not exist',
                'data' => []
            ], 200);
        }

        $checkName = Race::where('name', $request->name)->whereNot('id', $id)->where('isDeleted', false)->first();

        if ($checkName != "") {
            return response()->json([
                'status' => 400,
                'message' => 'Another event with this name already exists',
                'data' => []
            ], 200);
        }

        if ($request->name)
            $race->name = $request->name;
        if ($request->description)
            $race->description = $request->description;
        if ($request->requirements)
            $race->requirements = $request->requirements;
        if ($request->startDate)
            $race->startDate = $request->startDate;
        if ($request->endDate)
            $race->endDate = $request->endDate;

        $race->save();

        return response()->json([
            'status' => 200,
            'message' => 'Gala event updated successfully.',
            'data' => $race
        ], 200);
    }

    // Function to delete race
    public function deleteRace(Request $request, string $id)
    {
        $race = Race::where('id', $id)->where('isDeleted', false)->first();

        if ($race == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This event does not exist',
                'data' => []
            ], 200);
        }

        $race->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Gala event deleted successfully.',
            'data' => []
        ], 200);
    }

    // Function to create race group
    public function createGroup(NewRaceGroupDto $request)
    {

        $race = Race::where('id', $request->raceId)->where('isDeleted', false)->first();

        if ($race == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This event does not exist',
                'data' => []
            ], 200);
        }

        $raceGroup = new RaceGroup;
        $raceGroup->name = $request->name;
        $raceGroup->description = $request->description;
        $raceGroup->raceId = $request->raceId;
        $raceGroup->save();

        return response()->json([
            'status' => 201,
            'message' => 'Event group created successfully.',
            'data' => $raceGroup
        ], 201);
    }

    // Function to get all groups
    public function getAllRaceGroups(Request $request, string $id)
    {
        $search = $request->has('query') ? $request->get('query') : '';

        $groups = RaceGroup::where('raceId', $id)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->with('raceMembers')->get();

        return response()->json([
            'status' => 200,
            'message' => 'Event groups fetched successfully.',
            'data' => $groups,
        ], 200);
    }

    public function updateRaceGroup(UpdateRaceGroupDto $request, string $id)
    {
        $raceGroup = RaceGroup::where('id', $id)->where('isDeleted', false)->first();

        if ($raceGroup == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This group does not exist',
                'data' => []
            ], 200);
        }

        if ($request->name)
            $raceGroup->name = $request->name;
        if ($request->description)
            $raceGroup->description = $request->description;

        $raceGroup->save();

        return response()->json([
            'status' => 200,
            'message' => 'Group updated successfully.',
            'data' => $raceGroup
        ], 200);
    }

    // Function to delete gala group
    public function deleteRaceGroup(Request $request, string $id)
    {
        $raceGroup = RaceGroup::where('id', $id)->where('isDeleted', false)->first();

        if ($raceGroup == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This group does not exist',
                'data' => []
            ], 200);
        }

        $raceGroup->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Group deleted successfully.',
            'data' => []
        ], 200);
    }

    // Function to add swimmer to group
    public function addRaceMember(NewRaceGroupMemberDto $request)
    {
        // Check if group exists
        $raceGroup = RaceGroup::where('id', $request->raceGroupId)->where('isDeleted', false)->first();

        if ($raceGroup == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This group does not exist',
                'data' => []
            ], 200);
        }

        $newMember = new RacePerformance;
        $newMember->name = $request->name;
        $newMember->age = $request->age;
        $newMember->club = $request->club;
        $newMember->raceGroupId = $request->raceGroupId;

        if ($request->swimmerId) {
            // Check if user exists
            $user = User::where('id', $request->swimmerId)
                ->where('role', 'swimmer')
                ->where('isDeleted', false)->first();

            if ($user == '') {
                return response()->json([
                    'status' => 400,
                    'message' => 'This user does not exist',
                    'data' => []
                ], 200);
            }

            // Check if this user has already been added to this group
            $checkMember = RacePerformance::where('userId', $request->swimmerId)
                ->where('raceGroupId', $request->raceGroupId)
                ->where('isDeleted', false)->first();

            if ($checkMember != '') {
                return response()->json([
                    'status' => 400,
                    'message' => 'This user has already been added to this group.',
                    'data' => []
                ], 200);
            }

            // Over write data with current user data
            $newMember->userId = $request->swimmerId;
            $newMember->name = $user->firstName . ' ' . $user->lastName;
            $newMember->age = $this->getAgeDifference($user->dateOfBirth);
            $newMember->club = '';
        }

        $newMember->save();

        return response()->json([
            'status' => 200,
            'message' => 'Swimmer added to group successfully.',
            'data' => $newMember
        ], 200);

    }

    // Function to update race group swimmer details 
    public function updateRaceMember(UpdateRaceGroupMemberDto $request, string $id)
    {
        // Check if swimmer exists in group
        $swimmer = RacePerformance::where('id', $id)->where('isDeleted', false)->first();

        if ($swimmer == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This gala swimmer does not exist.',
                'data' => []
            ], 200);
        }

        if ($request->time)
            $swimmer->time = $request->time;
        if ($request->strokeId)
            $swimmer->strokeId = $request->strokeId;
        if ($request->rank)
            $swimmer->rank = $request->rank;
        if ($request->points)
            $swimmer->points = $request->points;

        $swimmer->save();

        // Recaliberate positions
        $this->adjustPosition($swimmer->raceGroupId);

        return response()->json([
            'status' => 201,
            'message' => 'Swimmer details updated successfully.',
            'data' => $swimmer
        ], 201);
    }

    // Function to delete swimmer from a group
    public function deleteRaceMember(Request $request, string $id)
    {
        // Check if swimmer exists in group
        $swimmer = RacePerformance::where('id', $id)->where('isDeleted', false)->first();

        if ($swimmer == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This gala swimmer does not exist.',
                'data' => []
            ], 200);
        }

        $swimmer->delete();

        return response()->json([
            'status' => 201,
            'message' => 'Swimmer details deleted successfully.',
            'data' => []
        ], 201);
    }

    // Function to recalibrate swimmer position
    public function adjustPosition(string $raceGroupId)
    {
        // Check if swimmer exists in group
        $swimmers = RacePerformance::where('raceGroupId', $raceGroupId)->orderBy('points', 'DESC')
        ->where('isDeleted', false)->get();

        for ($i = 0; $i < $swimmers->count(); $i++) {
            $swimmers[$i]->place = $i + 1;
            $swimmers[$i]->save();
        }
    }

    // Function to check the age of user
    public function getAgeDifference($dateOfBirth): int
    {
        $toDate = Carbon::parse($dateOfBirth);
        $fromDate = Carbon::parse('today');

        $years = $toDate->diffInYears($fromDate);

        return $years;
    }
}