<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::namespace('App\Http\Controllers')->group(function() {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::get('/user/posts', 'PostController@posts_by_user');

        Route::apiResource('posts', PostController::class);
        Route::post('/posts/{post}', 'PostController@update_photo')->whereAlphaNumeric('post');

    });
});
