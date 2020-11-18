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
            'name'      => ['required', 'max:255'],
            'email'     => ['required', 'email', 'unique:users', 'max:255'],
            'password'  => ['required', 'min:6', 'confirmed']
        ]);
        
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole('user');

        return response()->json([
            'message' =>'Successfully registered',
            'status'  => 1
        ]);
    }
}
