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

Route::middleware(['auth:api'])->group(function () {
    Route::get('/posts', 'PostController@index');
    Route::post('/posts', 'PostController@store');
    Route::post('/posts/{post}', 'PostController@show');
    Route::post('/posts/{post}/update', 'PostController@update');
    Route::get('/logout', 'LoginController@logout');
});
// Route::post('/posts', 'PostController@store');