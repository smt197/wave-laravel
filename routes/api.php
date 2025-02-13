<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get(
    '/v1/get',
    [AuthController::class, 'get']
);
Route::post('/v1/users/login', [AuthController::class, 'login']);
Route::post('/v1/users/register', [UserController::class, 'store']);
Route::post('/v1/clients/store', [ClientController::class, 'store']);

Route::middleware(['auth:api'])->prefix('v1')->group(function () {
    // Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    

    Route::patch('/clients/{id}/add-user', [ClientController::class, 'addUserToClient']);
    // Route::post('/clients/store', [ClientController::class, 'store']);

    Route::post('/transactions/transfer', [TransactionController::class, 'transfer']);
    Route::post('/transactions/deposit', [TransactionController::class, 'deposit']);
    Route::get('/transactions', [TransactionController::class, 'getTransactionByUser']);
    Route::get('/client/balance', [TransactionController::class, 'getBalance']);

    Route::post('/transactions/planifie', [TransactionController::class, 'scheduleTransfer']);

});

