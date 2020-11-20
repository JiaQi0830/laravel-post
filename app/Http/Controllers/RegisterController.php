<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterNewUserRequest;

class RegisterController extends Controller
{
    //
    public function register(RegisterNewUserRequest $request){
        try{
            if (isset($request->validator) && $request->validator->fails()) {
                $error = $request->validator->messages();

                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $error
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
