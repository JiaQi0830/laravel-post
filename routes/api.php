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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     Route::post('/posts', 'PostController@store');
    // return $request->user();
// });

Route::post('/login', 'LoginController@login');
Route::post('/register', 'RegisterController@register');
Route::get('/posts', 'PostController@index');
Route::get('/posts/{post}', 'PostController@show');

Route::middleware(['auth:api'])->group(function () {
    Route::get('/logout', 'LoginController@logout');
    Route::post('/posts/{post}/comment', 'PostController@comment');
    
    Route::middleware(['role:user'])->group(function () {
        Route::get('posts/{post}/like', 'PostController@like');
    });

    Route::middleware(['role:admin'])->group(function () {
        Route::post('/posts/{post}/update', 'PostController@update');
        Route::post('/posts', 'PostController@store');
    });
});
