<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MoleculeController;
use App\Models\Molecule;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'getUserInfo']);
});

Route::prefix('molecules')->group(function () {
    Route::get('/', [MoleculeController::class, 'getAllActive']);
    Route::get('/all', [MoleculeController::class, 'getAll'])->middleware('auth:sanctum');
    Route::get('/{id}', [MoleculeController::class, 'getById']);
    Route::post('/', [MoleculeController::class, 'create'])->middleware('auth:sanctum');
    Route::put('/{id}', [MoleculeController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [MoleculeController::class, 'delete'])->middleware('auth:sanctum');
});

Route::prefix('categories')->group( function() {
    Route::get('/', [CategoryController::class, 'getAllActive']);
    Route::get('/all', [CategoryController::class, 'getAll'])->middleware('auth:sanctum');
    Route::get('/{id}', [CategoryController::class, 'getById']);
    Route::post('/', [CategoryController::class, 'create'])->middleware('auth:sanctum');
    Route::put('/{id}', [CategoryController::class, 'update'])->middleware('auth:sanctum');
    Route::delete('/{id}', [CategoryController::class, 'delete'])->middleware('auth:sanctum');
});