<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke():JsonResponse
    {
        $credentials = request(['email','password']);
        if(!$token = auth(guard:'api')->attempt($credentials)){
            return response()->json(['error'=>'Unauthorized'],status:401);
        }
        return response()->json([
            'token'=>$token,
        ]);
    }
}
