<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Log;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        log::info("inside ady");
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
            return response()->json([
                'status' => 0
            ]);
        }

        return response()->json([
            'token' =>  $user->createToken('Auth Token')->accessToken,
            'role'  =>  $user->role,
            'status' => 1
        ]);
    }
}
