<?php

use App\Http\Controllers\PostController;
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

Route::group(['middleware' => ['auth:sanctum']] , function () {
    Route::get('/get-posts', [PostController::class , 'index']);
    Route::get('/get-post/search/{name}',[PostController::class , 'search']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
