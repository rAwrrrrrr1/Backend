<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registrationData = $request->all();

        $validate = Validator::make($registrationData, [
            'email' => 'required|email:rfc,dns|unique:users',
            'password' => ['required'],
            'nama' => 'required',
            'telepon' => 'required|regex:/^08[0-9]/',
        ]);

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $registrationData['password'] = bcrypt($request->password);

        $user = User::create($registrationData);

        return response([
            'success' => true,
            'message' => 'Register Success',
            'user' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if ($user && Hash::check($validatedData['password'], $user->password)) {
            Auth::login($user);
            $token = $user->createToken('Authentication Token')->accessToken;
            return response()->json([
                'success' => true,
                'message' => 'Authenticated',
                'user' => $user,
                'token_type' => 'Bearer',
                'access_token' => $token
            ]);
        } else {
            return response(['message' => 'Invalid Credentials'], 401);
        }
    }

    public function logout(){
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json([
            'success' => true,
            'message'=>'Logout Success'
        ],200);
    }

    public function listUsers()
    {
        $users = User::all();
        return response()->json([
            'success' => true,
            'message' => 'User List Retrieved',
            'users' => $users
        ], 200);
    }
}
