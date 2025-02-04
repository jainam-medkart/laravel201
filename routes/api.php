<?php

use App\Http\Controllers\AuthController;
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




// // User Routes
// Route::post('register', [AuthController::class, 'register']);
// Route::post('login', [AuthController::class, 'login']);
// Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// Route::get('user', [AuthController::class, 'getUserInfo'])->middleware('auth:sanctum');


// // Molecule Routes
// Route::get('molecules',[MoleculeController::class, 'getAllActive']);
// Route::get('molecules/all',[MoleculeController::class, 'getAll'])->middleware('auth:sanctum');
// Route::get('molecules/{id}',[MoleculeController::class, 'getById']);
// Route::post('molecules',[MoleculeController::class, 'create'])->middleware('auth:sanctum');
// Route::put('molecules/{id}',[MoleculeController::class, 'update'])->middleware('auth:sanctum');
// Route::delete('molecules/{id}',[MoleculeController::class, 'delete'])->middleware('auth:sanctum');

