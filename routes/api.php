<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DevelopPointController;
use App\Http\Controllers\MembersController;
use App\Http\Controllers\WorkingDaysController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\SlackChannelController;

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
    Route::prefix('channels')->group(function () {
        Route::get('/', [SlackChannelController::class, 'channels']);
    });
    Route::prefix('notifications')->group(function () {
        Route::post('/', [NotificationController::class, 'send']);
        Route::get('/', [NotificationController::class, 'index']);
    });
    Route::prefix('develop_points')->group(function () {
        Route::get('/', [DevelopPointController::class, 'index']);
    });
});

Route::get('health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');
