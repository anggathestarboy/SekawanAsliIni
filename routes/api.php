<?php

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
    Route::apiResource('/kategori', KategoriController::class);
});


