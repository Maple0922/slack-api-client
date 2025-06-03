<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\WorkingDaysController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamsController;

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

Route::middleware('api')->group(function () {
    Route::prefix('members')->group(function () {
        Route::get('/', [MembersController::class, 'index']);
        Route::post('/', [MembersController::class, 'create']);
        Route::put('/{id}', [MembersController::class, 'update']);
        Route::delete('/{id}', [MembersController::class, 'delete']);
    });
    Route::prefix('working_days')->group(function () {
        Route::get('/{monthOffset}', [WorkingDaysController::class, 'index']);
        Route::post('/', [WorkingDaysController::class, 'create']);
        Route::delete('/{date}/{memberId}', [WorkingDaysController::class, 'delete']);
    });
    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamsController::class, 'index']);
    });
    Route::prefix('notification')->group(function () {
        Route::post('/engineer_dev_point', [NotificationController::class, 'engineerDevPoint']);
        Route::post('/engineer_roadmap', [NotificationController::class, 'engineerRoadmap']);
    });
});

Route::get('health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');
