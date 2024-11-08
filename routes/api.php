<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/users/login', [AuthController::class, 'login']);
Route::post('/v1/users/register', [UserController::class, 'store']);
Route::post('/v1/clients/store', [ClientController::class, 'store']);

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
// ]    Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    // Route::get('/v1/users/export', [UserController::class, 'exportExcel']);
    
    // Route::apiResource('/clients', ClientController::class)->only(['index', 'store', 'show']);
    Route::patch('/clients/{id}/add-user', [ClientController::class, 'addUserToClient']);
    // Route::post('/clients/store', [ClientController::class, 'store']);






});

