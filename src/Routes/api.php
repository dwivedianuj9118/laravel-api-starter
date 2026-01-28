<?php

use Illuminate\Support\Facades\Route;
use Dwivedianuj9118\ApiStarter\Http\Controllers\HealthController;
use Dwivedianuj9118\ApiStarter\Http\Controllers\Auth\JwtAuthController;
use Dwivedianuj9118\ApiStarter\Http\Controllers\Sanctum\SanctumAuthController;

Route::prefix('api/' . config('api-starter.version'))
    ->middleware(['api','api.json', 'throttle:api'])
    ->group(function () {

        Route::get('/health', [HealthController::class, 'index']);

        // JWT AUTH
        if (config('api-starter.auth.jwt')) {
            Route::post('/auth/register', [JwtAuthController::class, 'register']);
            Route::post('/auth/login', [JwtAuthController::class, 'login']);
            Route::post('/auth/refresh', [JwtAuthController::class, 'refresh']);
            Route::post('/auth/logout', [JwtAuthController::class, 'logout'])
                ->middleware('auth:jwt');
        }

        // SANCTUM SPA AUTH
        if (config('api-starter.auth.sanctum')) {
            Route::post('/spa/login', [SanctumAuthController::class, 'login']);
            Route::post('/spa/logout', [SanctumAuthController::class, 'logout'])
                ->middleware('auth:sanctum');
        }
    });
