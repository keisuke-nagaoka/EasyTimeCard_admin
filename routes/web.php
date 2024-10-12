<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\WorktimeController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [EmployeeController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::group(['middleware' => ['auth']], function () {
    //従業員ルーティング
    Route::group(['prefix' => 'admin/{id}'], function () {
        Route::post('store', [EmployeeController::class, 'store'])->name('employees.store');
        Route::delete('destroy', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    Route::group(['middleware' => ['auth']], function () {
        //勤務時間一覧、作成ルーティング
        Route::group(['prefix' => 'employees/{employeeId}'], function() {
            Route::get('index', [WorktimeController::class, 'index'])->name('worktimes.index');
            Route::get('create', [WorktimeController::class, 'create'])->name('worktimes.create');
            Route::post('store', [WorktimeController::class, 'store'])->name('worktimes.store');
        });

        //勤務時間編集、更新ルーティング
        Route::group(['prefix' => 'employees/{employeeId}/worktimes/{worktimeId}'], function() {
            Route::get('edit', [WorktimeController::class, 'edit'])->name('worktimes.edit');
            Route::put('update', [WorktimeController::class, 'update'])->name('worktimes.update');
        });

    Route::resource('employees', EmployeeController::class, [ 'only' => ['create', 'show', 'edit', 'update']]);
    });
});

