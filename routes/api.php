<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TitleController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login'); 
    Route::middleware('auth:api')->group(function () {
        Route::delete('logout', 'logout');
        Route::get('user', 'getUserByToken');
        Route::post('edit', 'editUser');
    });
});

Route::controller(TitleController::class)->prefix('title')->group(function () {
    Route::get('/', 'index');
    Route::get('/mainPage', 'getMainPageTitles');
    Route::get('/catalog', 'getCatalogTitles');
    Route::get('/top', 'getTopTitles');
    Route::post('/', 'store');
    Route::post('genre-image', 'addImageToGenre');
    Route::get('search', 'findTitles');
    Route::middleware('auth:api')->group(function () {
        Route::post('/{id}', 'postComment');
        Route::post('/{id}/add-season', 'addSeasonToTitle');
        Route::post('/rate/{id}', 'rateTitle');
        Route::post('/like/{id}', 'likeTitle');
        Route::get('/profile', 'getProfileTitles');
    });
    Route::get('/{id}', 'getTitle');
});