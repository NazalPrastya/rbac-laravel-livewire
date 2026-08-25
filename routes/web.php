<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::group(['prefix' => 'dashboard'], function () {
        Route::view('/', 'dashboard')->name('dashboard');
        
        // User Management
        Route::group(['prefix' => 'user-management'], function () {
            Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
            
            Route::get('/role', [RoleController::class, 'index'])->name('role.index');
        });
    });
});

require __DIR__.'/settings.php';
