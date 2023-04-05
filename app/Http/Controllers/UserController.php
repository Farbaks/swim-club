<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\NewUserDto;
use App\Http\Requests\Users\SigninUserDto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use STS\JWT\Facades\JWT;

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

        $user = new User;
        $user->firstName = $request->firstName;
        $user->lastName = $request->lastName;
        $user->emailAddress = $request->emailAddress;
        $user->password = Hash::make($request->password);
        $user->phoneNumber = $request->phoneNumber;
        $user->dateOfBirth = date("Y-m-d", strtotime($request->dateOfBirth));
        $user->pictureUrl = $request->pictureUrl;
        $user->role = $request->role;
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
}