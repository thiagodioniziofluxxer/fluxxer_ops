<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeployController;
use App\Http\Controllers\HostController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VersionController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
        Route::resource('users', UserController::class)->middleware('can:usersViewAny,App\Models\User');
        Route::resource('roles', RoleController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('hosts', HostController::class);
        Route::resource('versions', VersionController::class);
        Route::resource('deploys', DeployController::class);
    });

});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
