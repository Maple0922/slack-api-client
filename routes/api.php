<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MembersController;
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
    Route::prefix('teams')->group(function () {
        Route::get('/', [TeamsController::class, 'index']);
    });
});

Route::get('health', function () {
    return response()->json(['status' => 'ok']);
})->name('health');
