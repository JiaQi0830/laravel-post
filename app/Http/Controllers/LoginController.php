<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use Log;

class LoginController extends Controller
{
    //
    public function login(Request $request){
        try{
            
            $validator = Validator::make($request->all(),[
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);
            
            if($validator->fails()){
                return response()->json([
                    'message' => 'Invalid data',
                    'error'   => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)){
                throw new Exception('The provided credentials are incorrect');
            }
            if($user->hasRole('admin')){
                $role = User::ADMIN;
            }elseif($user->hasRole('user')){
                $role = User::USER;
            }

            return response()->json([
                'message'   => 'Success',
                'data'      => ['token' =>  $user->createToken('Auth Token')->accessToken,
                                'role'  =>  $role]
            ], 200);

        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }
    }

    public function logout(){
        try{
            Auth::user()->AauthAcessToken()->delete();
            return response()->json(['message' => 'Successfully log out'], 200);
        } catch (Exception $ex){
            $message = $ex->getMessage();
            return response()->json(['message' => $message], 500);
        }

    }
}
