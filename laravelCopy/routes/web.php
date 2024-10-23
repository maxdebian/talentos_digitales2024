<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseProductController;
use App\Http\Controllers\TestSendEmailController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});


Route::group([
    'middleware'    =>  ['auth','isClient'],
    'prefix'    =>  'obras_sociales'
],function(){
    Route::get('/crearObraSocial',[App\Http\Controllers\ObraSocialController::class,'create'])
    ->name('obras_sociales.crearObraSocial');
    Route::post('/storeObraSocial',[App\Http\Controllers\ObraSocialController::class,'store'])
    ->name('obras_sociales.storeObraSocial');

});
/*
Route::group([
    'prefix'    =>  'users'
],function(){
    Route::
})

 */

 Route::get('/testEmail',[App\Http\Controllers\TestSendEmailController::class,'sendEmailTest'])->name('testEmail')->middleware('isAdmin');











Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::group([
    //'middleware'    =>  ['auth','adminUserRole'],
    'middleware'    =>  'auth',
    'prefix'        =>  'user'
],function(){
    Route::get('create',[App\Http\Controllers\UserController::class,'create'])->name('user.create');
    Route::get('list',[App\Http\Controllers\UserController::class,'index'])->name('user.index');
    Route::get('{user}/edit',[App\Http\Controllers\UserController::class,'edit'])->name('user.edit');
    Route::post('store',[App\Http\Controllers\UserController::class,'store'])->name('user.store');
    Route::post('search',[App\Http\Controllers\UserController::class,'searchUser'])->name('user.search');
    Route::get('destroy',[App\Http\Controllers\UserController::class,'destroy'])->name('user.destroy');
    Route::put('{user}/update', [App\Http\Controllers\UserController::class,'update'])->name('user.update');
    Route::get('show',[App\Http\Controllers\UserController::class,'show'])->name('user.show');

});
Route::resource('provider', ProviderController::class)/* ->middleware('adminUserRole') */;
Route::resource('product', ProductController::class)/* ->middleware('adminUserRole') */;
Route::resource('purchase', PurchaseProductController::class)/* ->middleware('adminUserRole') */;
