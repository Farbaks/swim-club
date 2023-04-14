<?php

namespace App\Http\Controllers;

use App\Http\Requests\Relationships\UpdateRelationshipDto;
use App\Http\Requests\Users\NewUserDto;
use App\Http\Requests\Users\NewWardDto;
use App\Http\Requests\Users\SigninUserDto;
use App\Http\Requests\Users\UpdatePasswordDto;
use App\Http\Requests\Users\UpdateUserDto;
use App\Models\Race;
use App\Models\RacePerformance;
use App\Models\Relationship;
use App\Models\Squad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use STS\JWT\Facades\JWT;
use Carbon\Carbon;

class UserController extends Controller
{
    //
    // Function to sign up users
    public function signup(NewUserDto $request)
    {

        $checkUser = User::where('emailAddress', $request->emailAddress)->first();

        if ($checkUser != "") {
            return response()->json([
                'status' => 400,
                'message' => 'An account with this email address already exists',
                'data' => []
            ], 200);
        }

        $checkUser = User::where('phoneNumber', $request->phoneNumber)->first();

        if ($checkUser != "") {
            return response()->json([
                'status' => 400,
                'message' => 'An account with this phone number already exists',
                'data' => []
            ], 200);
        }

        // For underage users
        if ($this->getAgeDifference($request->dateOfBirth) < 18) {

            return response()->json([
                'status' => 400,
                'message' => 'User with "' . $request->role . '" role has to be above the age of 18.',
                'data' => []
            ], 200);
        }

        $user = new User;
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->emailAddress = $request->emailAddress;
        $user->password = Hash::make($request->password);
        $user->phoneNumber = $request->phoneNumber;
        $user->dateOfBirth = $request->dateOfBirth;
        $user->pictureUrl = $request->pictureUrl;
        $user->role = $request->role;
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user->postcode = $request->postcode;
        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'User account has been created',
            'apiToken' => $this->generateAPIToken($user),
            'data' => $user
        ], 201);

    }

    // Function to sign in users
    public function signin(SigninUserDto $request)
    {

        $user = User::where('emailAddress', $request->emailAddress)->first();

        // Check if user email exists
        if ($user == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Email account does not exist',
                'data' => []
            ], 200);
        }

        // Check if password is correct
        if (!Hash::check($request->password, $user->password)) {
            // The passwords don't match...
            return response()->json([
                'status' => 400,
                'message' => 'Email or password incorrect',
                'data' => []
            ], 200);
        }

        // Generate api_token
        return response()->json([
            'status' => 200,
            'message' => 'Login succesful',
            'apiToken' => $this->generateAPIToken($user),
            'data' => $user
        ], 200);
    }

    public function refreshToken(Request $request)
    {
        $user = User::where('id', $request->userId)->first();

        // Check if user email exists
        if ($user == "") {
            return response()->json([
                'status' => 400,
                'message' => 'Account does not exist',
                'data' => []
            ], 200);
        }

        // Generate api_token
        return response()->json([
            'status' => 200,
            'message' => 'Token refresh succesful.',
            'apiToken' => $this->generateAPIToken($user),
            'data' => $user
        ], 200);
    }

    // Function to fetch users
    public function getAllUsers(Request $request)
    {
        // Log::channel('stderr')->debug($request->get('role'));

        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $role = $request->has('role') ? $request->get('role') : '';
        $search = $request->has('query') ? $request->get('query') : '';

        $users = User::offset(($page - 1) * $limit)->limit($limit)
            ->where('role', 'like', '%' . $role . '%')
            ->where(function ($query) use ($search) {
                $query->where('firstName', 'like', '%' . $search . '%')
                    ->orWhere('lastName', 'like', '%' . $search . '%');
            })->get();

        $usersCount = User::where('role', 'like', '%' . $role . '%')
            ->where(function ($query) use ($search) {
                $query->where('firstName', 'like', '%' . $search . '%')
                    ->orWhere('lastName', 'like', '%' . $search . '%');
            })->count();

        return response()->json([
            'status' => 200,
            'message' => 'Users fetched successfully.',
            'data' => $users,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $usersCount
            ]
        ], 200);
    }

    // Function to fetch on user
    public function getOneUser(Request $request, $id)
    {
        $user = User::find($id);

        if ($user == '') {
            return response()->json([
                'status' => 400,
                'message' => 'This user does not exist',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'message' => 'User fetched successfully.',
            'data' => $user
        ], 200);

    }

    // Function to update user info
    public function updateOneUser(UpdateUserDto $request)
    {
        $user = User::where('id', $request->userId)->first();

        // Check if user is underage
        $checkAge = $this->getAgeDifference($user->dateOfBirth);

        if ($checkAge < 18) {
            return response()->json([
                'status' => 400,
                'message' => 'Please reach out to your guardian to update your account info.',
                'data' => []
            ], 200);
        }

        // Check if email is taken
        if ($request->emailAddress) {
            $checkUser = User::where('emailAddress', $request->emailAddress)
                ->whereNot('id', $request->userId)->first();

            if ($checkUser != "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'An account with this email address already exists',
                    'data' => []
                ], 200);
            }
        }

        // Check if phone number is taken
        if ($request->phoneNumber) {
            $checkUser = User::where('phoneNumber', $request->phoneNumber)
                ->whereNot('id', $request->userId)->first();

            if ($checkUser != "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'An account with this phone number already exists',
                    'data' => []
                ], 200);
            }
        }

        if ($request->firstName)
            $user->firstName = $request->firstName;
        if ($request->lastName)
            $user->lastName = $request->lastName;
        if ($request->emailAddress)
            $user->emailAddress = $request->emailAddress;
        if ($request->phoneNumber)
            $user->phoneNumber = $request->phoneNumber;
        if ($request->pictureUrl)
            $user->pictureUrl = $request->pictureUrl;
        if ($request->gender)
            $user->gender = $request->gender;
        if ($request->address)
            $user->address = $request->address;
        if ($request->postcode)
            $user->postcode = $request->postcode;

        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'User account has been updated',
            'data' => $user
        ], 201);
    }

    // Function to update password
    public function updatePassword(UpdatePasswordDto $request)
    {
        $user = User::where('id', $request->userId)->first();

        // Check if password is correct
        if (!Hash::check($request->oldPassword, $user->password)) {
            // The passwords don't match...
            return response()->json([
                'status' => 400,
                'message' => 'Incorrect current password',
                'data' => []
            ], 200);
        }

        $user->password = Hash::make($request->newPassword);

        $user->save();

        // Generate api_token
        return response()->json([
            'status' => 200,
            'message' => 'Password changed successfully.',
            'data' => $user
        ], 200);
    }

    // Function to create a ward
    public function createWard(NewWardDto $request)
    {
        $checkUser = User::where('emailAddress', $request->emailAddress)->first();

        if ($checkUser != "") {
            return response()->json([
                'status' => 400,
                'message' => 'An account with this email address already exists',
                'data' => []
            ], 200);
        }

        $checkUser = User::where('phoneNumber', $request->phoneNumber)->first();

        if ($checkUser != "") {
            return response()->json([
                'status' => 400,
                'message' => 'An account with this phone number already exists',
                'data' => []
            ], 200);
        }

        $user = new User;
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->emailAddress = $request->emailAddress;
        $user->password = Hash::make($request->password);
        $user->phoneNumber = $request->phoneNumber;
        $user->dateOfBirth = $request->dateOfBirth;
        $user->pictureUrl = $request->pictureUrl;
        $user->role = 'swimmer';
        $user->gender = $request->gender;
        $user->address = $request->address;
        $user->postcode = $request->postcode;
        $user->save();

        $relationship = new Relationship;
        $relationship->guardianId = $request->userId;
        $relationship->wardId = $user->id;
        $relationship->status = 'active';
        $relationship->save();

        return response()->json([
            'status' => 201,
            'message' => 'User account has been created',
            'data' => $user
        ], 201);
    }

    // Function to update ward's info
    public function updateRelationshipInfo(UpdateUserDto $request, string $id)
    {

        $user = User::where('id', $request->id)->first();

        // Check if email is taken
        if ($request->emailAddress) {
            $checkUser = User::where('emailAddress', $request->emailAddress)
                ->whereNot('id', $request->id)->first();

            if ($checkUser != "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'An account with this email address already exists',
                    'data' => []
                ], 200);
            }
        }

        // Check if phone number is taken
        if ($request->phoneNumber) {
            $checkUser = User::where('phoneNumber', $request->phoneNumber)
                ->whereNot('id', $request->id)->first();

            if ($checkUser != "") {
                return response()->json([
                    'status' => 400,
                    'message' => 'An account with this phone number already exists',
                    'data' => []
                ], 200);
            }
        }

        if ($request->firstName)
            $user->firstName = $request->firstName;
        if ($request->lastName)
            $user->lastName = $request->lastName;
        if ($request->emailAddress)
            $user->emailAddress = $request->emailAddress;
        if ($request->phoneNumber)
            $user->phoneNumber = $request->phoneNumber;
        if ($request->pictureUrl)
            $user->pictureUrl = $request->pictureUrl;
        if ($request->gender)
            $user->gender = $request->gender;
        if ($request->address)
            $user->address = $request->address;
        if ($request->postcode)
            $user->postcode = $request->postcode;

        $user->save();

        return response()->json([
            'status' => 201,
            'message' => 'User account has been updated',
            'data' => $user
        ], 201);
    }

    // Get underage swimmers for a parent
    public function getRelationships(Request $request)
    {
        $status = $request->has('status') ? $request->get('status') : 'active';

        $relationship = Relationship::where(($request->userRole == 'parent' ? 'guardianId' : 'wardId'), $request->userId)
            ->where('status', 'like', $status . '%')
            ->with(['ward', 'guardian'])->get();

        return response()->json([
            'status' => 200,
            'message' => 'Relationships fetched successfully.',
            'data' => $relationship,
        ], 200);
    }

    // Function to fetch all swimmers with their performance records
    public function getSwimmers(Request $request)
    {
        // Filter and Sorts
        $page = $request->has('page') ? $request->get('page') : 0;
        $limit = $request->has('limit') ? $request->get('limit') : 20;

        $squad = $request->has('squad') ? $request->get('squad') : '';
        $search = $request->has('query') ? $request->get('query') : '';

        $performances = User::where('role', 'swimmer')
            ->offset(($page - 1) * $limit)->limit($limit)
            ->join('squad_members', 'users.id', '=', 'squad_members.userId')
            ->join('squads', 'squad_members.squadId', '=', 'squads.id')
            ->where(function ($query) use ($search) {
                $query->where('users.firstName', 'like', '%' . $search . '%')
                    ->orWhere('users.lastName', 'like', '%' . $search . '%');
            })
            ->where('squads.name', 'like', '%' . $squad . '%')
            ->select('users.id', 'users.firstName', 'users.lastName', 'users.dateOfBirth', 'users.gender', 'squads.name as squad')
            ->orderBy('users.created_at', 'desc')
            ->get();

        foreach ($performances as $performance) {
            $performance['firstPlaceCount'] = RacePerformance::where('userId', $performance->id)->where('place', 1)->count();
            $performance['secondPlaceCount'] = RacePerformance::where('userId', $performance->id)->where('place', 2)->count();
            $performance['thirdPlaceCount'] = RacePerformance::where('userId', $performance->id)->where('place', 3)->count();

            $bestTime = RacePerformance::where('userId', $performance->id)
                ->join('race_groups', 'race_performances.raceGroupId', '=', 'race_groups.id')
                ->select('race_groups.name as group', 'race_performances.time', 'race_performances.points')
                ->orderBy('race_performances.points', 'desc')->first();

            $performance['bestResultGroup'] = $bestTime->group;
            $performance['bestResultTime'] = $bestTime->time;
            $performance['bestResultPoints'] = $bestTime->points;
        }

        $performancesCount = User::where('role', 'swimmer')
            ->join('squad_members', 'users.id', '=', 'squad_members.userId')
            ->join('squads', 'squads.id', '=', 'squad_members.squadId')
            ->where(function ($query) use ($search) {
                $query->where('users.firstName', 'like', '%' . $search . '%')
                    ->orWhere('users.lastName', 'like', '%' . $search . '%');
            })
            ->where('squads.name', 'like', '%' . $squad . '%')
            ->select('users.*')
            ->orderBy('created_at', 'desc')
            ->count();

        return response()->json([
            'status' => 200,
            'message' => 'Swimmer Performances fetched successfully.',
            'data' => $performances,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'count' => $performancesCount
            ]
        ], 200);
    }

    // Function to fetch dashboard report 
    public function getReport()
    {

        $report = [];

        $report['numOfCoaches'] = User::where('role', 'coach')->where('isDeleted', false)->count();
        $report['numOfSquads'] = Squad::where('isDeleted', false)->count();
        $report['numberofSwimmers'] = User::where('role', 'swimmer')->where('isDeleted', false)->count();

        $report['upcomingGalas'] = Race::where('startDate', '>=', date("Y-m-d"))->orderBy('startDate', 'asc')->get();

        $subQuery = User::join('race_performances', 'users.id', '=', 'race_performances.userId')
            ->selectRaw('users.*')
            ->selectRaw('count(*) as occurence')
            ->selectRaw('race_performances.place as place')
            ->selectRaw('CASE
                WHEN race_performances.place = 1 THEN 3 * count(place)
                WHEN race_performances.place = 2 THEN 2 * count(place)
                WHEN race_performances.place = 3 THEN 1 * count(place)
                ELSE 0
                end as points')
            ->groupByRaw('users.id, place');

        $report['swimmers'] = DB::query()->fromSub($subQuery, "n1")
            ->selectRaw('id, firstName, lastName, dateOfBirth, gender, sum(points) as totalPoints')
            ->groupBy('id')
            ->orderBy('totalPoints', 'desc')
            ->limit(5)
            ->get();

        foreach ($report['swimmers'] as $performance) {
            $performance->firstPlaceCount = RacePerformance::where('userId', $performance->id)->where('place', 1)->count();
            $performance->secondPlaceCount = RacePerformance::where('userId', $performance->id)->where('place', 2)->count();
            $performance->thirdPlaceCount = RacePerformance::where('userId', $performance->id)->where('place', 3)->count();

            $bestTime = RacePerformance::where('userId', $performance->id)
                ->join('race_groups', 'race_performances.raceGroupId', '=', 'race_groups.id')
                ->select('race_groups.name as group', 'race_performances.time', 'race_performances.points')
                ->orderBy('race_performances.points', 'desc')->first();

            $performance->bestResultGroup = $bestTime->group;
            $performance->bestResultTime = $bestTime->time;
            $performance->bestResultPoints = $bestTime->points;
        }


        return response()->json([
            'status' => 200,
            'message' => 'Report fetched successfully.',
            'data' => $report
        ], 200);
    }

    // Function to generate token
    public function generateAPIToken(User $user)
    {

        $data = [
            'userId' => $user->id,
            'userRole' => $user->role
        ];
        $encrypted = JWT::get(env('APP_KEY'), $data, 3600);

        return $encrypted;

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