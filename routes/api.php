<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminTaskController;
use App\Http\Controllers\UserTaskController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminWithdrawalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EarningController;
use App\Http\Controllers\UserController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user/name/{sponsor_id}', [UserController::class, 'getUserNameBySponsorId']);

Route::middleware('auth:api')->group(function () {
    Route::get('/user', [UserController::class, 'userDetails']);
    Route::get('/referrals/direct', [UserController::class, 'directReferrals']);
    Route::get('/referrals/all-levels', [UserController::class, 'allLevelReferrals']);
    Route::get('/tasks', [UserTaskController::class, 'fetchTasks']);
    Route::post('/tasks/{task}/complete', [UserTaskController::class, 'completeTask']);
    Route::get('/earnings', [UserController::class, 'userEarnings']);
    Route::post('/withdrawals', [UserController::class, 'createWithdrawalRequest']);
    Route::get('/withdrawals', [UserController::class, 'getWithdrawals']);
    Route::get('/stats', [UserController::class, 'getStats']);
    Route::get('/earnings', [EarningController::class, 'index']);
    Route::get('/earnings/{earning}', [EarningController::class, 'show']);
});

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/users', [AdminAuthController::class, 'listUsers']);
    Route::post('/admin/tasks', [AdminTaskController::class, 'create']);
    Route::put('/admin/tasks/{task}', [AdminTaskController::class, 'update']);
    Route::get('/admin/tasks', [AdminTaskController::class, 'list']);
    Route::get('/admin/withdrawals', [AdminWithdrawalController::class, 'listWithdrawalRequests']);
    Route::put('/admin/withdrawals/{withdrawal}', [AdminWithdrawalController::class, 'updateWithdrawalRequestStatus']);
});
