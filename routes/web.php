<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
})->middleware('auth.basic');

Route::get('/member', fn() =>
Inertia::render('Member/Index'))->middleware(['auth', 'verified'])->name('member');
Route::get('/working_days', fn() =>
Inertia::render('WorkingDays/Index'))->middleware(['auth', 'verified'])->name('working_days');
Route::get('/notification', fn() =>
Inertia::render('Notification/Index'))->middleware(['auth', 'verified'])->name('notification');
Route::get('/dev_point', fn() =>
Inertia::render('DevelopPoint/Index'))->middleware(['auth', 'verified'])->name('develop_point');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
