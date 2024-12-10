<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|max:255'
        ]);
        $user = User::select('id','name','password','email')->where('email',$request->email)->first();
        /* dd($user); */
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
                'message'=>'The provided credential are incorrect'
            ],401);
        }

        $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
        return response()->json([
            'mensaje'=>'Login Successfull',
            'token_type'=> 'Bearer',
            'token'=>$token,
            'user'=>$user
        ],200);

    }
    public function register(Request $request) : JsonResponse{
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        if($user){
            $token = $user->createToken($user->name.'Auth-Token')->plainTextToken;
            return response()->json([
                'message'=>'Registration Successfull',
                'token_type'=> 'Bearer',
                'token'=>$token,
                'user'=>$user
            ],201);
        }else{
            return response()->json([
                'message'=>'Something went wrong!'
            ],500);
        }
    }
    public function logout(Request $request):JsonResponse{
        $user = User::where('id',$request->id)->first();
        if($user){
            $user->tokens()->delete();
            return response()->json([
                'message'=>'Logout successfuly',
            ],200);
        }else{
            return response()->json([
                'message'=>'User Not Found'
            ],404);
        }
    }
    public function profile(Request $request):JsonResponse{
        if($request->user()){
            return response()->json([
                'message'=>'Profile Fetched',
                'data'  => $request->user()
            ],200);
        }else{
            return response()->json([
                'message'=>'Not Authenticated'
            ],401);
        }
    }
}

