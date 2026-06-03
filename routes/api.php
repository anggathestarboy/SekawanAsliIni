<?php

use App\Http\Controllers\AlatController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/auth/register', [AuthController::class, "register"]);
Route::post('/auth/login', [AuthController::class, "login"]);

Route::middleware("auth:api")->group(function() {
    Route::post('/auth/logout', [AuthController::class, "logout"]);
    Route::apiResource('/kategori', KategoriController::class);
    Route::apiResource('/alat', AlatController::class);
    Route::apiResource('/pelanggan', App\Http\Controllers\PelangganController::class);
    Route::apiResource('/pelanggan_data', App\Http\Controllers\PelangganDataController::class);
    Route::apiResource('/penyewaan', App\Http\Controllers\PenyewaanController::class);
});


