<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::group(['prefix' => 'dashboard'], function () {
        Route::view('/', 'dashboard')->middleware('menu.permission:dashboard')->name('dashboard');

        // User Management
        Route::group(['prefix' => 'user-management'], function () {
            Route::get('/menu', [MenuController::class, 'index'])->middleware('menu.permission:user-management.menu')->name('menu.index');

            Route::get('/role', [RoleController::class, 'index'])->middleware('menu.permission:user-management.role')->name('role.index');

            Route::get('/user', [UserController::class, 'index'])->middleware('menu.permission:user-management.user')->name('user.index');
        });
    });
});

require __DIR__.'/settings.php';
