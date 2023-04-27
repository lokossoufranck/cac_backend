<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes(['verify' => true]);

Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () { 
    Route::get('email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name    
});


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('home', function () {
    return redirect('/dashboard');
});
Route::get('/app/{id}', [App\Http\Controllers\AppController::class, 'index'])->name('app');


Route::get('/{vue_capture?}', function () {
    return view('home');
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');
