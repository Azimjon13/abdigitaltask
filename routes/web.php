<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Telegram\Bot\Laravel\Facades\Telegram;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('telegrambot', function (){
    return Telegram::bot('abdigitalbot')->getMe();
})->name('telegrambot');

Route::post('telegrambot/'.env('TELEGRAM_BOT_TOKEN').'/webhook', function (){
    return Telegram::bot('abdigitalbot')->getMe();
})->name('weebhook');

Route::get('posts', 'PostController@index')->name('posts');
Route::get('posts/{id}', 'PostController@show')->whereNumber('id')->name('posts.show');

//Route::resource('posts', PostController::class)->middleware('auth:sanctum');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
