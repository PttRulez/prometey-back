<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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


Route::post('/session-log', 'SessionLogController@store')->withoutMiddleware(['auth', \App\Http\Middleware\VerifyCsrfToken::class]);
Route::get('/session-log/fake', 'SessionLogController@fake')->withoutMiddleware(['auth']);

//Auth::routes([
//    'register' => false, // Registration Routes...
//    'reset' => false, // Password Reset Routes...
//    'verify' => false, // Email Verification Routes...
//]);

//Route::resource('/users', 'UserController')->middleware(['auth']);

Route::get('/{any}', 'StartController@start')->where('any', '.*')->middleware(['auth']);
