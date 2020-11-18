<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Log;

class RegisterController extends Controller
{
    //
    public function register(Request $request){
        try{

            $validator = Validator::make($request->all(),[
                'name'      => ['required', 'max:255'],
                'email'     => ['required', 'email', 'unique:users', 'max:255'],
                'password'  => ['required', 'min:6', 'confirmed']
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password' => Hash::make($request->password)
            ]);

            $user->assignRole('user');

            return response()->json([
                'message' =>'Successfully registered'
            ]);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }

    }
}
