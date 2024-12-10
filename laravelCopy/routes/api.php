<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\LogoutController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\TestController;
use App\Http\Controllers\API\AuthController;

Route::post('/login',[AuthController::class,'login']);
Route::post('/register',[AuthController::class,'register']);
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('/logout',[AuthController::class,'logout']);
    Route::get('/profile',[AuthController::class,'profile']);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('authJWT'); */

/* Route::group([
    'middleware'    =>  'api',
    'prefix'        =>  'auth',
],function(){
    Route::post('/login',LoginController::class);
    Route::post('/logout',LoginController::class);
}); */

//Route::resource('products', ProductController::class);
Route::resource('test', TestController::class);
Route::apiResource('products',ProductController::class)/* ->middleware('auth:sanctum') */ ;

/*
Route::get('/test',function(){
    return 'our API';
});
 */

