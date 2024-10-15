<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::group([
/*     'middleware'    =>  'auth' */
    'prefix'    =>  'obras_sociales'
],function(){
    Route::get('/crearObraSocial',[App\Http\Controllers\ObraSocialController::class,'create'])->name('obras_sociales.crearObraSocial');
    Route::post('/storeObraSocial',[App\Http\Controllers\ObraSocialController::class,'store'])->name('obras_sociales.storeObraSocial');

});
/*
Route::group([
    'prefix'    =>  'users'
],function(){
    Route::
})

 */












Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
