<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use Illuminate\Support\Facades\Route;

// Satu-satunya endpoint publik
Route::post('/login', [AuthController::class, 'login']);

// Semua endpoint lain wajib memakai token Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::apiResource('barang', BarangController::class);
});
