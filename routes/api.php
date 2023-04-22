<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

 
Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::resource('users', 'App\Http\Controllers\UserController');
    Route::get('/auth/user', 'App\Http\Controllers\AuthController@me');
 
    Route::resource('projects', 'App\Http\Controllers\ProjectController');
    Route::resource('tasks', 'App\Http\Controllers\TaskController');
    Route::post('/auth/logout', 'App\Http\Controllers\AuthController@logout');
});

Route::post('/auth/register', 'App\Http\Controllers\AuthController@register');
Route::post('/auth/login', 'App\Http\Controllers\AuthController@login');
