<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Log;

class RegisterController extends Controller
{
    //
    public function register(Request $request){
        $request->validate([
            'name'      => ['required'],
            'email'     => ['required', 'email', 'unique:users'],
            'password'  => ['required', 'min:6'],
            'role'      => ['required']
        ]);
        
        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' =>'Successfully registered',
            'status'  => 1
        ]);
    }
}
