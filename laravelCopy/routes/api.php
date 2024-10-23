<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('authJWT'); */

Route::group([
    'middleware'    =>  'api',
    'prefix'        =>  'auth',
],function(){
    Route::post('/login',LoginController::class);
    Route::post('/logout',LoginController::class);
});
