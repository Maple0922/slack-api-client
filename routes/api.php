<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\NotionController;

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

Route::prefix('/webhook')->group(function () {
    Route::prefix('/notion')->group(function () {
        Route::prefix('/roadmap')->group(function () {
            Route::post('/created', [NotionController::class, 'roadmapCreated'])->name('webhook.notion.roadmap.created');
        });
    });
});
