<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Log;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);
        
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect']
            ]);
        }

        if($user->hasRole('admin')){
            $role = User::ADMIN;
        }else{
            $role = User::USER;
        }

        return response()->json([
            'token' =>  $user->createToken('Auth Token')->accessToken,
            'role'  =>  $role
        ]);
    }

    public function logout(){
        if (Auth::check()) {
            Auth::user()->AauthAcessToken()->delete();
         }
    }
}
