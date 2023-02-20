<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = new User();

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->available_vacation_days = 0;
        $user->password = Hash::make($request->password);
        
        $user->save();
        
        return response()->json([
            'user' => $user,
            'token' => $user->createToken('app')->plainTextToken
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where(['email' => $request->email])->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Incorrect credentials'
            ]);
        }

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('app')->plainTextToken
        ], 200);
    }
}
