<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


//AUTH API
Route::prefix('v1')->group(function (){
    Route::prefix('auth')->group(function (){
        Route::post('login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])
            ->name('api.auth.login');
        Route::post('register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])
            ->name('api.auth.register');
        Route::post('logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
            ->name('api.auth.logout');
    });
});
