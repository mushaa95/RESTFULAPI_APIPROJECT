<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        try{
            // Validate
            $validateUser = Validator::make($request->all(),[
                "name" => "required",
                "email" => "required|email|unique:users,email",
                "password" => "required"
            ]);

            if($validateUser->fails()){
                return response()->json([
                    "status" => false,
                    "message" => "validation error",
                    "errors" => $validateUser->errors()
                ], 401);
            }

            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password)
            ]);

            return response()->json([
                "status" => true,
                "message" => "User created successfully",
                "token" => $user->createToken("API_TOKEN")->plainTextToken
            ], 200);

        }catch(\Throwable $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request){
        try{
            // Validate
            $validateUser = Validator::make($request->all(), [
                "email" => "required",
                "password" => "required"
            ]);

            if($validateUser->fails()){
                return response()->json([
                    "status" => false,
                    "message" => "validation error",
                    "errors" => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(["email", "password"]))){
                return response()->json([
                    "status" => false,
                    "message" => "Email and/or password does not exist"
                ]);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                "status" => true,
                "message" => "Logged in successfully",
                "token" => $user->createToken("API_TOKEN")->plainTextToken
            ]);

        }catch(\Throwable $e){
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
