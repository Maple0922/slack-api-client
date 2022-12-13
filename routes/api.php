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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/sds')->group(function () {
    Route::get('/count', [App\Http\Controllers\SDsController::class, 'count']);
    Route::get('/list', [App\Http\Controllers\SDsController::class, 'errors']);
});
Route::prefix('/errors')->group(function () {
    Route::get('/count', [App\Http\Controllers\ErrorsController::class, 'count']);
    Route::get('/list', [App\Http\Controllers\ErrorsController::class, 'errors']);
});
